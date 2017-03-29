<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );
if ( !class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

if ( !class_exists( 'SH_List_table' ) ) {

	class SH_List_table extends WP_List_Table {

		public function __construct() {
			if ( !is_dir( SH_ROOT . 'temp/' ) ) {
				mkdir( SH_ROOT . 'temp/' );
			}
			global $status, $page;
			parent::__construct(
					array(
						'singular' => 'plugin',
						'plural' => 'plugins',
						'ajax' => false,
					)
			);
		}

		protected function _gather_plugin_data() {
			SH_update_init::$instance->admin_init();
			SH_update_init::$instance->thickbox();
			$table_data = array();
			$i = 0;
			$installed_plugins = get_plugins();

			foreach ( SH_update_init::$instance->plugins as $plugin ) {
				if ( is_plugin_active( $plugin['file_path'] ) )
					continue;

				$table_data[$i]['sanitized_plugin'] = $plugin['name'];
				$table_data[$i]['slug'] = $this->_get_plugin_data_from_name( $plugin['name'] );
				$external_url = $this->_get_plugin_data_from_name( $plugin['name'], 'external_url' );
				$source = $this->_get_plugin_data_from_name( $plugin['name'], 'source' );
				if ( $external_url && preg_match( '|^http(s)?://|', $external_url ) ) {
					$table_data[$i]['plugin'] = '<a href="' . esc_url( $external_url ) . '" title="' . $plugin['name'] . '" target="_blank">' . $plugin['name'] . '</a>';
				} elseif ( !$source || preg_match( '|^http://wordpress.org/extend/plugins/|', $source ) ) {
					$url = esc_url( add_query_arg(
									array(
						'tab' => 'plugin-information',
						'plugin' => $this->_get_plugin_data_from_name( $plugin['name'] ),
						'TB_iframe' => 'true',
						'width' => '640',
						'height' => '500',
									), admin_url( 'plugin-install.php' )
							) );

					$table_data[$i]['plugin'] = '<a href="' . esc_url( $url ) . '" class="thickbox" title="' . $plugin['name'] . '">' . $plugin['name'] . '</a>';
				} else {
					$table_data[$i]['plugin'] = '' . $plugin['name'] . '';
				}

				if ( isset( $table_data[$i]['plugin'] ) && (array) $table_data[$i]['plugin'] )
					$plugin['name'] = $table_data[$i]['plugin'];

				if ( isset( $plugin['external_url'] ) ) {
					$table_data[$i]['source'] = __( 'External Link', SH_NAME );
				} elseif ( isset( $plugin['source'] ) ) {
					if ( preg_match( '|^http(s)?://|', $plugin['source'] ) )
						$table_data[$i]['source'] = __( 'Private Repository', SH_NAME );
					else
						$table_data[$i]['source'] = __( 'Pre-Packaged', SH_NAME );
				}
				else {
					$table_data[$i]['source'] = __( 'WordPress Repository', SH_NAME );
				}
				$table_data[$i]['type'] = $plugin['required'] ? __( 'Required', SH_NAME ) : __( 'Recommended', SH_NAME );
				if ( !isset( $installed_plugins[$plugin['file_path']] ) )
					$table_data[$i]['status'] = sprintf( '%1$s', __( 'Not Installed', SH_NAME ) );
				elseif ( is_plugin_inactive( $plugin['file_path'] ) )
					$table_data[$i]['status'] = sprintf( '%1$s', __( 'Installed But Not Activated', SH_NAME ) );

				$table_data[$i]['file_path'] = $plugin['file_path'];
				$table_data[$i]['url'] = isset( $plugin['source'] ) ? $plugin['source'] : 'repo';

				$i++;
			}
			$resort = array();
			$req = array();
			$rec = array();
			foreach ( $table_data as $plugin )
				$resort[] = $plugin['type'];

			foreach ( $resort as $type )
				if ( 'Required' == $type )
					$req[] = $type;
				else
					$rec[] = $type;

			sort( $req );
			sort( $rec );
			array_merge( $resort, $req, $rec );
			array_multisort( $resort, SORT_DESC, $table_data );

			return $table_data;
		}

		protected function _get_plugin_data_from_name( $name, $data = 'slug' ) {
			foreach ( SH_update_init::$instance->plugins as $plugin => $values ) {
				if ( $name == $values['name'] && isset( $values[$data] ) )
					return $values[$data];
			}
			return false;
		}

		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'source':
				case 'type':
				case 'status':
					return $item[$column_name];
			}
		}

		public function column_plugin( $item ) {
			$installed_plugins = get_plugins();
			if ( is_plugin_active( $item['file_path'] ) )
				$actions = array();

			if ( !isset( $installed_plugins[$item['file_path']] ) ) {
				$actions = array(
					'install' => sprintf(
							'<a href="%1$s" title="Install %2$s">' . __( 'Install', SH_NAME ) . '</a>', esc_url( wp_nonce_url(
											add_query_arg(
													array(
						'page' => SH_update_init::$instance->menu,
						'plugin' => $item['slug'],
						'plugin_name' => $item['sanitized_plugin'],
						'plugin_source' => $item['url'],
						'duffer-install' => 'install-plugin',
													), admin_url( SH_update_init::$instance->parent_url_slug )
											), 'duffer-install'
							) ), $item['sanitized_plugin']
					),
				);
			} elseif ( is_plugin_inactive( $item['file_path'] ) ) {
				$actions = array(
					'activate' => sprintf(
							'<a href="%1$s" title="Activate %2$s">' . __( 'Activate', SH_NAME ) . '</a>', esc_url( add_query_arg(
											array(
						'page' => SH_update_init::$instance->menu,
						'plugin' => $item['slug'],
						'plugin_name' => $item['sanitized_plugin'],
						'plugin_source' => $item['url'],
						'duffer-activate' => 'activate-plugin',
						'duffer-activate-nonce' => wp_create_nonce( 'duffer-activate' ),
											), admin_url( SH_update_init::$instance->parent_url_slug
											)
							) ), $item['sanitized_plugin']
					),
				);
			}

			return sprintf( '%1$s %2$s', $item['plugin'], $this->row_actions( $actions ) );
		}

		public function column_cb( $item ) {
			$value = $item['file_path'] . ',' . $item['url'] . ',' . $item['sanitized_plugin'];
			return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" id="%3$s" />', $this->_args['singular'], $value, $item['sanitized_plugin'] );
		}

		public function no_items() {

			printf( __( 'No plugins to install or activate. <a href="%1$s" title="Return to the Dashboard">Return to the Dashboard</a>', SH_NAME ), admin_url() );
			echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
		}

		public function get_columns() {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'plugin' => __( 'Plugin', SH_NAME ),
				'source' => __( 'Source', SH_NAME ),
				'type' => __( 'Type', SH_NAME ),
				'status' => __( 'Status', SH_NAME )
			);
			return $columns;
		}

		public function get_bulk_actions() {
			$actions = array(
				'duffer-bulk-install' => __( 'Install', SH_NAME ),
				'duffer-bulk-activate' => __( 'Activate', SH_NAME ),
			);
			return $actions;
		}

		public function process_bulk_actions() {
			if ( 'duffer-bulk-install' === $this->current_action() ) {
				check_admin_referer( 'bulk-' . $this->_args['plural'] );
				$plugins_to_install = array();
				$plugin_installs = array();
				$plugin_path = array();
				$plugin_name = array();
				if ( isset( $_GET[sanitize_key( 'plugins' )] ) )
					$plugins = explode( ',', stripslashes( $_GET[sanitize_key( 'plugins' )] ) );
				elseif ( isset( $_POST[sanitize_key( 'plugin' )] ) )
					$plugins = (array) $_POST[sanitize_key( 'plugin' )];
				else
					$plugins = array();

				$a = 0;
				if ( isset( $_POST[sanitize_key( 'plugin' )] ) ) {
					foreach ( $plugins as $plugin_data )
						$plugins_to_install[] = explode( ',', $plugin_data );

					foreach ( $plugins_to_install as $plugin_data ) {
						$plugin_installs[] = $plugin_data[0];
						$plugin_path[] = $plugin_data[1];
						$plugin_name[] = $plugin_data[2];
					}
				} else {
					foreach ( $plugins as $key => $value ) {
						if ( 0 == $key % 3 || 0 == $key ) {
							$plugins_to_install[] = $value;
							$plugin_installs[] = $value;
						}
						$a++;
					}
				}
				if ( isset( $_GET[sanitize_key( 'plugin_paths' )] ) )
					$plugin_paths = explode( ',', stripslashes( $_GET[sanitize_key( 'plugin_paths' )] ) );
				elseif ( isset( $_POST[sanitize_key( 'plugin' )] ) )
					$plugin_paths = (array) $plugin_path;
				else
					$plugin_paths = array();

				if ( isset( $_GET[sanitize_key( 'plugin_names' )] ) )
					$plugin_names = explode( ',', stripslashes( $_GET[sanitize_key( 'plugin_names' )] ) );
				elseif ( isset( $_POST[sanitize_key( 'plugin' )] ) )
					$plugin_names = (array) $plugin_name;
				else
					$plugin_names = array();

				$b = 0;
				foreach ( $plugin_installs as $key => $plugin ) {
					if ( preg_match( '|.php$|', $plugin ) ) {
						unset( $plugin_installs[$key] );
						if ( !isset( $_GET[sanitize_key( 'plugin_paths' )] ) )
							unset( $plugin_paths[$b] );
						if ( !isset( $_GET[sanitize_key( 'plugin_names' )] ) )
							unset( $plugin_names[$b] );
					}
					$b++;
				}
				if ( empty( $plugin_installs ) )
					return false;

				$plugin_installs = array_values( $plugin_installs );
				$plugin_paths = array_values( $plugin_paths );
				$plugin_names = array_values( $plugin_names );
				$plugin_installs = array_map( 'urldecode', $plugin_installs );
				$plugin_paths = array_map( 'urldecode', $plugin_paths );
				$plugin_names = array_map( 'urldecode', $plugin_names );
				$url = wp_nonce_url(
						esc_url( add_query_arg(
										array(
					'page' => SH_update_init::$instance->menu,
					'duffer-action' => 'install-selected',
					'plugins' => urlencode( implode( ',', $plugins ) ),
					'plugin_paths' => urlencode( implode( ',', $plugin_paths ) ),
					'plugin_names' => urlencode( implode( ',', $plugin_names ) ),
										), admin_url( SH_update_init::$instance->parent_url_slug )
						) ), 'bulk-plugins'
				);
				$method = '';
				$fields = array( sanitize_key( 'action' ), sanitize_key( '_wp_http_referer' ), sanitize_key( '_wpnonce' ) );
				if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $fields ) ) )
					return true;

				if ( !WP_Filesystem( $creds ) ) {
					request_filesystem_credentials( $url, $method, true, false, $fields );
					return true;
				}
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				$api = array();
				$sources = array();
				$install_path = array();
				$c = 0;
				foreach ( $plugin_installs as $plugin ) {
					$api[$c] = plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) ) ? plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) ) : (object) $api[$c] = 'duffer-empty';
					$c++;
				}
				if ( is_wp_error( $api ) )
					wp_die( SH_update_init::$instance->strings['oops'] . var_dump( $api ) );

				$d = 0;
				foreach ( $api as $object ) {
					$sources[$d] = isset( $object->download_link ) && 'repo' == $plugin_paths[$d] ? $object->download_link : $plugin_paths[$d];
					$d++;
				}
				$url = esc_url( add_query_arg( array( 'page' => SH_update_init::$instance->menu ), admin_url( SH_update_init::$instance->parent_url_slug ) ) );
				$nonce = 'bulk-plugins';
				$names = $plugin_names;
				$auth = new SH_server_auth();
				$verify = $auth->sh_verification( 'check' );
				if ( $verify == 'Confirmed' ) {

					$download_plugins = sh_set( $_POST, 'plugin' );
					foreach ( $download_plugins as $d ) {
						if ( strpos( $d, '.zip' ) !== false ) {
							$construct = explode( ',', $d );
							$link = $auth->sh_get_plugin( sh_set( $construct, '2' ) );
							$auth->sh_curl( $link, sh_set( $construct, '0' ) );
						}
					}
					$isDirEmpty = SH_ROOT . 'temp/';
					if ( $isDirEmpty ) {
						$installer = new SH_Bulk_installer( $skin = new SH_Bulk_installer_skins( compact( 'url', 'nonce', 'names' ) ) );
						echo '<div class="duffer wrap">';
						echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';
						$installer->bulk_install( $sources );
						echo '</div>';
						array_map( 'unlink', glob( SH_ROOT . 'temp/*' ) );
						rmdir( SH_ROOT . 'temp/' );
						return true;
					}
				} else {
					echo $verify;
				}
				return true;
			}
			if ( 'duffer-bulk-activate' === $this->current_action() ) {
				check_admin_referer( 'bulk-' . $this->_args['plural'] );
				$plugins = isset( $_POST[sanitize_key( 'plugin' )] ) ? (array) $_POST[sanitize_key( 'plugin' )] : array();
				$plugins_to_activate = array();
				foreach ( $plugins as $i => $plugin )
					$plugins_to_activate[] = explode( ',', $plugin );

				foreach ( $plugins_to_activate as $i => $array ) {
					if ( !preg_match( '|.php$|', $array[0] ) )
						unset( $plugins_to_activate[$i] );
				}
				if ( empty( $plugins_to_activate ) )
					return;

				$plugins = array();
				$plugin_names = array();

				foreach ( $plugins_to_activate as $plugin_string ) {
					$plugins[] = $plugin_string[0];
					$plugin_names[] = $plugin_string[2];
				}

				$count = count( $plugin_names );
				$last_plugin = array_pop( $plugin_names );
				$imploded = empty( $plugin_names ) ? '' . $last_plugin . '' : '' . ( implode( ', ', $plugin_names ) . ' and ' . $last_plugin . '.' );
				$activate = activate_plugins( $plugins );
				if ( is_wp_error( $activate ) )
					echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
				else
					printf( '<div id="message" class="updated"><p>%1$s %2$s</p></div>', _n( 'The following plugin was activated successfully:', 'The following plugins were activated successfully:', $count, SH_NAME ), $imploded );

				$recent = (array) get_option( 'recently_activated' );
				foreach ( $plugins as $plugin => $time )
					if ( isset( $recent[$plugin] ) )
						unset( $recent[$plugin] );

				update_option( 'recently_activated', $recent );
				unset( $_POST );
			}
		}

		public function prepare_items() {
			$per_page = 100;
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->process_bulk_actions();
			$this->items = $this->_gather_plugin_data();
		}

	}

}
