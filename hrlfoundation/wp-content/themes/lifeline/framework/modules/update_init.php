<?php
if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_update_init {

	static $instance;
	public $menu = 'install-required-plugins';
	public $plugins = array();
	public $parent_url_slug = 'themes.php';
	public $parent_menu_slug = 'themes.php';
	public $default_path = '';
	public $has_notices = true;
	public $is_automatic = false;
	public $message = '';
	public $strings = array();

	public function __construct() {
		self::$instance = &$this;
		$files = glob( SH_ROOT . "framework/update/*.php" );
		foreach ( $files as $file ) {
			if ( !is_dir( $file ) ) {
				require_once( $file );
			}
		}
		$this->strings = array(
			'page_title' => __( 'Install Required Plugins', SH_NAME ),
			'menu_title' => __( 'Install Plugins', SH_NAME ),
			'installing' => __( 'Installing Plugin: %s', SH_NAME ),
			'oops' => __( 'Something went wrong.', SH_NAME ),
			'notice_can_install_required' => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ),
			'notice_can_install_recommended' => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ),
			'notice_cannot_install' => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ),
			'notice_can_activate_required' => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ),
			'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ),
			'notice_cannot_activate' => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ),
			'notice_ask_to_update' => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ),
			'notice_cannot_update' => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ),
			'install_link' => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return' => __( 'Return to Required Plugins Installer', SH_NAME ),
			'plugin_activated' => __( 'Plugin activated successfully.', SH_NAME ),
			'complete' => __( 'All plugins installed and activated successfully. %1$s', SH_NAME ),
		);
		do_action_ref_array( 'duffer_init', array( &$this ) );
		add_action( 'init', array( &$this, 'init' ) );
	}

	public function init() {
		do_action( 'duffer_register' );
		if ( $this->plugins ) {
			$sorted = array();
			foreach ( $this->plugins as $plugin )
				$sorted[] = $plugin['name'];

			array_multisort( $sorted, SORT_ASC, $this->plugins );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'admin_head', array( &$this, 'dismiss' ) );
			add_filter( 'install_plugin_complete_actions', array( &$this, 'actions' ) );

			if ( $this->is_duffer_page() ) {
				remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
				remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 );
				add_action( 'wp_head', 'wp_admin_bar_render', 1000 );
				add_action( 'admin_head', 'wp_admin_bar_render', 1000 );
			}

			if ( $this->has_notices ) {
				add_action( 'admin_notices', array( &$this, 'notices' ) );
				add_action( 'admin_init', array( &$this, 'admin_init' ), 1 );
				add_action( 'admin_enqueue_scripts', array( &$this, 'thickbox' ) );
				add_action( 'switch_theme', array( &$this, 'update_dismiss' ) );
			}

			foreach ( $this->plugins as $plugin ) {
				if ( isset( $plugin['force_activation'] ) && true === $plugin['force_activation'] ) {
					add_action( 'admin_init', array( &$this, 'force_activation' ) );
					break;
				}
			}

			foreach ( $this->plugins as $plugin ) {
				if ( isset( $plugin['force_deactivation'] ) && true === $plugin['force_deactivation'] ) {
					add_action( 'switch_theme', array( &$this, 'force_deactivation' ) );
					break;
				}
			}
		}
	}

	public function admin_init() {
		if ( !$this->is_duffer_page() )
			return;

		if ( isset( $_REQUEST['tab'] ) && 'plugin-information' == $_REQUEST['tab'] ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			wp_enqueue_style( 'plugin-install' );
			global $tab, $body_id;
			$body_id = $tab = 'plugin-information';
			install_plugin_information();
			exit;
		}
	}

	public function thickbox() {
		if ( !get_user_meta( get_current_user_id(), 'duffer_dismissed_notice', true ) )
			add_thickbox();
	}

	public function admin_menu() {
		if ( !current_user_can( 'install_plugins' ) )
			return;

		$this->populate_file_path();
		foreach ( $this->plugins as $plugin ) {
			if ( !is_plugin_active( $plugin['file_path'] ) ) {
				add_theme_page(
						$this->strings['page_title'], $this->strings['menu_title'], 'edit_theme_options', $this->menu, array( &$this, 'install_plugins_page' )
				);
				break;
			}
		}
	}

	public function install_plugins_page() {
		$plugin_table = new SH_List_table;
		if ( isset( $_POST[sanitize_key( 'action' )] ) && 'duffer-bulk-install' == $_POST[sanitize_key( 'action' )] && $plugin_table->process_bulk_actions() || $this->do_plugin_install() )
			return;
		?>
		<div class="duffer wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php $plugin_table->prepare_items(); ?>
			<?php if ( isset( $this->message ) ) echo wp_kses_post( $this->message ); ?>
			<form id="duffer-plugins" action="" method="post">
				<input type="hidden" name="duffer-page" value="<?php echo $this->menu; ?>" />
				<?php $plugin_table->display(); ?>
			</form>
		</div>
		<?php
	}

	protected function do_plugin_install() {
		$plugin = array();
		if ( isset( $_GET[sanitize_key( 'plugin' )] ) && ( isset( $_GET[sanitize_key( 'duffer-install' )] ) && 'install-plugin' == $_GET[sanitize_key( 'duffer-install' )] ) ) {
			check_admin_referer( 'duffer-install' );

			$p_name = sh_set( $_GET, 'plugin_name' );
			$z_name = sh_set( $_GET, 'plugin' );
			$p_source = sh_set( $_GET, 'plugin_source' );
			$auth = new SH_server_auth();
			$verify = $auth->sh_verification( 'check' );
			if ( $verify == 'Confirmed' ) {
				if ( $p_source != 'repo' ) {
					$link = $auth->sh_get_plugin( $p_name );
					$auth->sh_curl( $link, $z_name );
					$isDirEmpty = SH_ROOT . 'temp/';
					if ( $isDirEmpty ) {
						$plugin['name'] = $_GET[sanitize_key( 'plugin_name' )];
						$plugin['slug'] = $_GET[sanitize_key( 'plugin' )];
						$plugin['source'] = $_GET[sanitize_key( 'plugin_source' )];
						$url = wp_nonce_url(
								esc_url( add_query_arg(
												array(
							'page' => $this->menu,
							'plugin' => $plugin['slug'],
							'plugin_name' => $plugin['name'],
							'plugin_source' => $plugin['source'],
							'duffer-install' => 'install-plugin',
												), admin_url( $this->parent_url_slug )
										), 'duffer-install'
								) );
						$method = '';
						$fields = array( sanitize_key( 'duffer-install' ) );
						if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $fields ) ) )
							return true;

						if ( !WP_Filesystem( $creds ) ) {
							request_filesystem_credentials( $url, $method, true, false, $fields );
							return true;
						}
						require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
						require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
						if ( isset( $plugin['source'] ) && 'repo' == $plugin['source'] ) {
							$api = plugins_api( 'plugin_information', array( 'slug' => $plugin['slug'], 'fields' => array( 'sections' => false ) ) );
							if ( is_wp_error( $api ) )
								wp_die( $this->strings['oops'] . var_dump( $api ) );

							if ( isset( $api->download_link ) )
								$plugin['source'] = $api->download_link;
						}
						$type = preg_match( '|^http(s)?://|', $plugin['source'] ) ? 'web' : 'upload';
						$title = sprintf( $this->strings['installing'], $plugin['name'] );
						$url = esc_url( add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $plugin['slug'] ), 'update.php' ) );
						if ( isset( $_GET['from'] ) )
							$url .= esc_url( add_query_arg( 'from', urlencode( stripslashes( $_GET['from'] ) ), $url ) );

						$nonce = 'install-plugin_' . $plugin['slug'];
						$source = ( 'upload' == $type ) ? $this->default_path . $plugin['source'] : $plugin['source'];
						$upgrader = new Plugin_Upgrader( $skin = new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
						$upgrader->install( $source );
						wp_cache_flush();
						if ( $this->is_automatic ) {
							$plugin_activate = $upgrader->plugin_info();
							$activate = activate_plugin( $plugin_activate );
							$this->populate_file_path();

							if ( is_wp_error( $activate ) ) {
								echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
								echo '<p><a href="' . esc_url( add_query_arg( 'page', $this->menu, admin_url( $this->parent_url_slug ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . __( 'Return to Required Plugins Installer', SH_NAME ) . '</a></p>';
								return true;
							} else {
								echo '<p>' . $this->strings['plugin_activated'] . '</p>';
							}
						}

						$complete = array();
						foreach ( $this->plugins as $plugin ) {
							if ( !is_plugin_active( $plugin['file_path'] ) ) {
								echo '<p><a href="' . esc_url( add_query_arg( 'page', $this->menu, admin_url( $this->parent_url_slug ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . $this->strings['return'] . '</a></p>';
								$complete[] = $plugin;
								break;
							} else {
								$complete[] = '';
							}
						}

						$complete = array_filter( $complete );
						if ( empty( $complete ) ) {
							echo '<p>' . sprintf( $this->strings['complete'], '<a href="' . admin_url() . '" title="' . __( 'Return to the Dashboard', SH_NAME ) . '">' . __( 'Return to the Dashboard', SH_NAME ) . '</a>' ) . '</p>';
							echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
						}
						array_map( 'unlink', glob( SH_ROOT . 'temp/*' ) );
						rmdir( SH_ROOT . 'temp/' );
						return true;
					}
				} else {
					$plugin['name'] = $_GET[sanitize_key( 'plugin_name' )];
					$plugin['slug'] = $_GET[sanitize_key( 'plugin' )];
					$plugin['source'] = $_GET[sanitize_key( 'plugin_source' )];
					$url = wp_nonce_url(
							esc_url( add_query_arg(
											array(
						'page' => $this->menu,
						'plugin' => $plugin['slug'],
						'plugin_name' => $plugin['name'],
						'plugin_source' => $plugin['source'],
						'duffer-install' => 'install-plugin',
											), admin_url( $this->parent_url_slug )
									), 'duffer-install'
							) );
					$method = '';
					$fields = array( sanitize_key( 'duffer-install' ) );
					if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $fields ) ) )
						return true;

					if ( !WP_Filesystem( $creds ) ) {
						request_filesystem_credentials( $url, $method, true, false, $fields );
						return true;
					}
					require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
					require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
					if ( isset( $plugin['source'] ) && 'repo' == $plugin['source'] ) {
						$api = plugins_api( 'plugin_information', array( 'slug' => $plugin['slug'], 'fields' => array( 'sections' => false ) ) );
						if ( is_wp_error( $api ) )
							wp_die( $this->strings['oops'] . var_dump( $api ) );

						if ( isset( $api->download_link ) )
							$plugin['source'] = $api->download_link;
					}
					$type = preg_match( '|^http(s)?://|', $plugin['source'] ) ? 'web' : 'upload';
					$title = sprintf( $this->strings['installing'], $plugin['name'] );
					$url = esc_url( add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $plugin['slug'] ), 'update.php' ) );
					if ( isset( $_GET['from'] ) )
						$url .= esc_url( add_query_arg( 'from', urlencode( stripslashes( $_GET['from'] ) ), $url ) );

					$nonce = 'install-plugin_' . $plugin['slug'];
					$source = ( 'upload' == $type ) ? $this->default_path . $plugin['source'] : $plugin['source'];
					$upgrader = new Plugin_Upgrader( $skin = new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
					$upgrader->install( $source );
					wp_cache_flush();
					if ( $this->is_automatic ) {
						$plugin_activate = $upgrader->plugin_info();
						$activate = activate_plugin( $plugin_activate );
						$this->populate_file_path();

						if ( is_wp_error( $activate ) ) {
							echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
							echo '<p><a href="' . esc_url( add_query_arg( 'page', $this->menu, admin_url( $this->parent_url_slug ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . __( 'Return to Required Plugins Installer', SH_NAME ) . '</a></p>';
							return true;
						} else {
							echo '<p>' . $this->strings['plugin_activated'] . '</p>';
						}
					}

					$complete = array();
					foreach ( $this->plugins as $plugin ) {
						if ( !is_plugin_active( $plugin['file_path'] ) ) {
							echo '<p><a href="' . esc_url( add_query_arg( 'page', $this->menu, admin_url( $this->parent_url_slug ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . $this->strings['return'] . '</a></p>';
							$complete[] = $plugin;
							break;
						} else {
							$complete[] = '';
						}
					}

					$complete = array_filter( $complete );
					if ( empty( $complete ) ) {
						echo '<p>' . sprintf( $this->strings['complete'], '<a href="' . admin_url() . '" title="' . __( 'Return to the Dashboard', SH_NAME ) . '">' . __( 'Return to the Dashboard', SH_NAME ) . '</a>' ) . '</p>';
						echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
					}
					return true;
				}
			} else {
				echo $verify;
				exit;
			}
		} elseif ( isset( $_GET[sanitize_key( 'plugin' )] ) && ( isset( $_GET[sanitize_key( 'duffer-activate' )] ) && 'activate-plugin' == $_GET[sanitize_key( 'duffer-activate' )] ) ) {
			check_admin_referer( 'duffer-activate', 'duffer-activate-nonce' );
			$plugin['name'] = $_GET[sanitize_key( 'plugin_name' )];
			$plugin['slug'] = $_GET[sanitize_key( 'plugin' )];
			$plugin['source'] = $_GET[sanitize_key( 'plugin_source' )];
			$plugin_data = get_plugins( '/' . $plugin['slug'] );
			$plugin_file = array_keys( $plugin_data );
			$plugin_to_activate = $plugin['slug'] . '/' . $plugin_file[0];
			$activate = activate_plugin( $plugin_to_activate );

			if ( is_wp_error( $activate ) ) {
				echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
				echo '<p><a href="' . esc_url( add_query_arg( 'page', $this->menu, admin_url( $this->parent_url_slug ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . $this->strings['return'] . '</a></p>';
				return true;
			} else {
				if ( !isset( $_POST[sanitize_key( 'action' )] ) ) {
					$msg = sprintf( __( 'The following plugin was activated successfully: %s.', SH_NAME ), '<strong>' . $plugin['name'] . '</strong>' );
					echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
				}
			}
		}
		return false;
	}

	public function notices() {
		global $current_screen;
		if ( $this->is_duffer_page() )
			return;

		$installed_plugins = get_plugins();
		$this->populate_file_path();
		$message = array();
		$install_link = false;
		$install_link_count = 0;
		$activate_link = false;
		$activate_link_count = 0;
		foreach ( $this->plugins as $plugin ) {
			if ( is_plugin_active( $plugin['file_path'] ) ) {
				if ( isset( $plugin['version'] ) ) {
					if ( isset( $installed_plugins[$plugin['file_path']]['Version'] ) ) {
						if ( version_compare( $installed_plugins[$plugin['file_path']]['Version'], $plugin['version'], '<' ) ) {
							if ( current_user_can( 'install_plugins' ) )
								$message['notice_ask_to_update'][] = $plugin['name'];
							else
								$message['notice_cannot_update'][] = $plugin['name'];
						}
					} else {
						continue;
					}
				} else {
					continue;
				}
			}
			if ( !isset( $installed_plugins[$plugin['file_path']] ) ) {
				$install_link = true;
				$install_link_count++;
				if ( current_user_can( 'install_plugins' ) ) {
					if ( $plugin['required'] )
						$message['notice_can_install_required'][] = $plugin['name'];
					else
						$message['notice_can_install_recommended'][] = $plugin['name'];
				}
				else {
					$message['notice_cannot_install'][] = $plugin['name'];
				}
			} elseif ( is_plugin_inactive( $plugin['file_path'] ) ) {
				$activate_link = true;
				$activate_link_count++;
				if ( current_user_can( 'activate_plugins' ) ) {
					if ( ( isset( $plugin['required'] ) ) && ( $plugin['required'] ) )
						$message['notice_can_activate_required'][] = $plugin['name'];
					else {
						$message['notice_can_activate_recommended'][] = $plugin['name'];
					}
				} else {
					$message['notice_cannot_activate'][] = $plugin['name'];
				}
			}
		}
		if ( !get_user_meta( get_current_user_id(), 'duffer_dismissed_notice', true ) ) {
			if ( !empty( $message ) ) {
				krsort( $message );
				$rendered = '';
				foreach ( $message as $type => $plugin_groups ) {
					$linked_plugin_groups = array();
					$count = count( $plugin_groups );
					foreach ( $plugin_groups as $plugin_group_single_name ) {
						$external_url = $this->_get_plugin_data_from_name( $plugin_group_single_name, 'external_url' );
						$source = $this->_get_plugin_data_from_name( $plugin_group_single_name, 'source' );

						if ( $external_url && preg_match( '|^http(s)?://|', $external_url ) ) {
							$linked_plugin_groups[] = '<a href="' . esc_url( $external_url ) . '" title="' . $plugin_group_single_name . '" target="_blank">' . $plugin_group_single_name . '</a>';
						} elseif ( !$source || preg_match( '|^http://wordpress.org/extend/plugins/|', $source ) ) {
							$url = esc_url( add_query_arg(
											array(
								'tab' => 'plugin-information',
								'plugin' => $this->_get_plugin_data_from_name( $plugin_group_single_name ),
								'TB_iframe' => 'true',
								'width' => '640',
								'height' => '500',
											), admin_url( 'plugin-install.php' )
									) );

							$linked_plugin_groups[] = '<a href="' . esc_url( $url ) . '" class="thickbox" title="' . $plugin_group_single_name . '">' . $plugin_group_single_name . '</a>';
						} else {
							$linked_plugin_groups[] = $plugin_group_single_name;
						}
						if ( isset( $linked_plugin_groups ) && (array) $linked_plugin_groups )
							$plugin_groups = $linked_plugin_groups;
					}

					$last_plugin = array_pop( $plugin_groups );
					$imploded = empty( $plugin_groups ) ? '' . $last_plugin . '' : '' . ( implode( ', ', $plugin_groups ) . __( ' and ', SH_NAME ) . $last_plugin . '' );
					$rendered .= '<p>' . sprintf( translate_nooped_plural( $this->strings[$type], $count, SH_NAME ), $imploded, $count ) . '</p>';
				}
				$show_install_link = $install_link ? '<a href="' . esc_url( add_query_arg( 'page', $this->menu, admin_url( $this->parent_url_slug ) ) ) . '">' . translate_nooped_plural( $this->strings['install_link'], $install_link_count, SH_NAME ) . '</a>' : '';
				$show_activate_link = $activate_link ? '<a href="' . admin_url( 'plugins.php' ) . '">' . translate_nooped_plural( $this->strings['activate_link'], $activate_link_count, SH_NAME ) . '</a>' : '';
				$action_links = apply_filters(
						'duffer_notice_action_links', array(
					'install' => ( current_user_can( 'install_plugins' ) ) ? $show_install_link : '',
					'activate' => ( current_user_can( 'activate_plugins' ) ) ? $show_activate_link : '',
					'dismiss' => '<a class="dismiss-notice" href="' . esc_url( add_query_arg( 'duffer-dismiss', 'dismiss_admin_notices' ) ) . '" target="_parent">' . __( 'Dismiss this notice', SH_NAME ) . '</a>',
						)
				);
				$action_links = array_filter( $action_links );
				if ( $action_links )
					$rendered .= '<p>' . implode( ' | ', $action_links ) . '</p>';
				if ( isset( $this->strings['nag_type'] ) )
					add_settings_error( 'duffer', 'duffer', $rendered, sanitize_html_class( strtolower( $this->strings['nag_type'] ), 'updated' ) );
				else
					add_settings_error( 'duffer', 'duffer', $rendered, 'updated' );
			}
		}
		if ( 'options-general' !== $current_screen->parent_base )
			settings_errors( 'duffer' );
	}

	public function dismiss() {
		if ( isset( $_GET[sanitize_key( 'duffer-dismiss' )] ) )
			update_user_meta( get_current_user_id(), 'duffer_dismissed_notice', 1 );
	}

	public function register( $plugin ) {
		if ( !isset( $plugin['slug'] ) || !isset( $plugin['name'] ) )
			return;

		$this->plugins[] = $plugin;
	}

	public function config( $config ) {
		$keys = array( 'default_path', 'parent_menu_slug', 'parent_url_slug', 'domain', 'has_notices', 'menu', 'is_automatic', 'message', 'strings' );
		foreach ( $keys as $key ) {
			if ( isset( $config[$key] ) ) {
				if ( is_array( $config[$key] ) ) {
					foreach ( $config[$key] as $subkey => $value )
						$this->{$key}[$subkey] = $value;
				} else {
					$this->$key = $config[$key];
				}
			}
		}
	}

	public function actions( $install_actions ) {
		if ( $this->is_duffer_page() )
			return false;

		return $install_actions;
	}

	public function populate_file_path() {
		foreach ( $this->plugins as $plugin => $values )
			$this->plugins[$plugin]['file_path'] = $this->_get_plugin_basename_from_slug( $values['slug'] );
	}

	protected function _get_plugin_basename_from_slug( $slug ) {
		$keys = array_keys( get_plugins() );
		foreach ( $keys as $key ) {
			if ( preg_match( '|^' . $slug . '|', $key ) )
				return $key;
		}
		return $slug;
	}

	protected function _get_plugin_data_from_name( $name, $data = 'slug' ) {
		foreach ( $this->plugins as $plugin => $values ) {
			if ( $name == $values['name'] && isset( $values[$data] ) )
				return $values[$data];
		}
		return false;
	}

	protected function is_duffer_page() {
		global $current_screen;
		if ( !is_null( $current_screen ) && $this->parent_menu_slug == $current_screen->parent_file && isset( $_GET['page'] ) && $this->menu === $_GET['page'] )
			return true;

		if ( isset( $_GET['page'] ) && $this->menu === $_GET['page'] )
			return true;

		return false;
	}

	public function update_dismiss() {
		delete_user_meta( get_current_user_id(), 'duffer_dismissed_notice' );
	}

	public function force_activation() {
		$this->populate_file_path();
		$installed_plugins = get_plugins();
		foreach ( $this->plugins as $plugin ) {
			if ( isset( $plugin['force_activation'] ) && $plugin['force_activation'] && !isset( $installed_plugins[$plugin['file_path']] ) )
				continue;

			elseif ( isset( $plugin['force_activation'] ) && $plugin['force_activation'] && is_plugin_inactive( $plugin['file_path'] ) ) {
				if ( isset( $plugin['file_path'] ) )
					activate_plugin( $plugin['file_path'] );
			}
		}
	}

	public function force_deactivation() {
		$this->populate_file_path();
		foreach ( $this->plugins as $plugin ) {
			if ( isset( $plugin['force_deactivation'] ) && $plugin['force_deactivation'] && is_plugin_active( $plugin['file_path'] ) )
				deactivate_plugins( $plugin['file_path'] );
		}
	}

}
