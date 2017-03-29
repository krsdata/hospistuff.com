<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_Bulk_installer_skins extends Bulk_Upgrader_Skin {

	public $plugin_info = array();
	public $plugin_names = array();
	public $i = 0;

	public function __construct( $args = array() ) {
		$defaults = array( 'url' => '', 'nonce' => '', 'names' => array() );
		$args = wp_parse_args( $args, $defaults );
		$this->plugin_names = $args['names'];
		parent::__construct( $args );
	}

	public function add_strings() {
		if ( SH_update_init::$instance->is_automatic ) {
			$this->upgrader->strings['skin_upgrade_start'] = __( 'The installation and activation process is starting. This process may take a while on some hosts, so please be patient.', SH_NAME );
			$this->upgrader->strings['skin_update_successful'] = __( '%1$s installed and activated successfully.', SH_NAME ) . ' <a onclick="%2$s" href="#" class="hide-if-no-js"><span>' . __( 'Show Details', SH_NAME ) . '</span><span class="hidden">' . __( 'Hide Details', SH_NAME ) . '</span>.</a>';
			$this->upgrader->strings['skin_upgrade_end'] = __( 'All installations and activations have been completed.', SH_NAME );
			$this->upgrader->strings['skin_before_update_header'] = __( 'Installing and Activating Plugin %1$s (%2$d/%3$d)', SH_NAME );
		} else {
			$this->upgrader->strings['skin_upgrade_start'] = __( 'The installation process is starting. This process may take a while on some hosts, so please be patient.', SH_NAME );
			$this->upgrader->strings['skin_update_failed_error'] = __( 'An error occurred while installing %1$s: <strong>%2$s</strong>.', SH_NAME );
			$this->upgrader->strings['skin_update_failed'] = __( 'The installation of %1$s failed.', SH_NAME );
			$this->upgrader->strings['skin_update_successful'] = __( '%1$s installed successfully.', SH_NAME ) . ' <a onclick="%2$s" href="#" class="hide-if-no-js"><span>' . __( 'Show Details', SH_NAME ) . '</span><span class="hidden">' . __( 'Hide Details', SH_NAME ) . '</span>.</a>';
			$this->upgrader->strings['skin_upgrade_end'] = __( 'All installations have been completed.', SH_NAME );
			$this->upgrader->strings['skin_before_update_header'] = __( 'Installing Plugin %1$s (%2$d/%3$d)', SH_NAME );
		}
	}

	public function before( $title = '' ) {
		$this->in_loop = true;
		printf( '<h4>' . $this->upgrader->strings['skin_before_update_header'] . ' <img alt="" src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="hidden waiting-' . $this->upgrader->update_current . '" style="vertical-align:middle;" /></h4>', $this->plugin_names[$this->i], $this->upgrader->update_current, $this->upgrader->update_count );
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').show();</script>';
		echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr( $this->upgrader->update_current ) . '"><p>';
		$this->before_flush_output();
	}

	public function after( $title = '' ) {
		echo '</p></div>';
		if ( $this->error || !$this->result ) {
			if ( $this->error )
				echo '<div class="error"><p>' . sprintf( sh_set( $this->upgrader->strings, 'skin_update_failed_error' ), $this->plugin_names[$this->i], $this->error ) . '</p></div>';
			else
				echo '<div class="error"><p>' . sprintf( sh_set( $this->upgrader->strings, 'skin_update_failed' ), $this->plugin_names[$this->i] ) . '</p></div>';

			echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js( $this->upgrader->update_current ) . '\').show();</script>';
		}
		if ( !empty( $this->result ) && !is_wp_error( $this->result ) ) {
			echo '<div class="updated"><p>' . sprintf( sh_set( $this->upgrader->strings, 'skin_update_successful' ), $this->plugin_names[$this->i], 'jQuery(\'#progress-' . esc_js( $this->upgrader->update_current ) . '\').toggle();jQuery(\'span\', this).toggle(); return false;' ) . '</p></div>';
			echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
		}
		$this->reset();
		$this->after_flush_output();
	}

	public function bulk_footer() {
		parent::bulk_footer();
		wp_cache_flush();
		$complete = array();
		foreach ( SH_update_init::$instance->plugins as $plugin ) {
			if ( !is_plugin_active( $plugin['file_path'] ) ) {
				echo '<p><a href="' . esc_url( add_query_arg( 'page', SH_update_init::$instance->menu, admin_url( SH_update_init::$instance->parent_url_slug ) ) ) . '" title="' . esc_attr( SH_update_init::$instance->strings['return'] ) . '" target="_parent">' . SH_update_init::$instance->strings['return'] . '</a></p>';
				$complete[] = $plugin;
				break;
			} else {
				$complete[] = '';
			}
		}
		$complete = array_filter( $complete );
		if ( empty( $complete ) ) {
			echo '<p>' . sprintf( SH_update_init::$instance->strings['complete'], '<a href="' . admin_url() . '" title="' . __( 'Return to the Dashboard', SH_NAME ) . '">' . __( 'Return to the Dashboard', SH_NAME ) . '</a>' ) . '</p>';
			echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
		}
	}

	public function before_flush_output() {
		wp_ob_end_flush_all();
		flush();
	}

	public function after_flush_output() {
		wp_ob_end_flush_all();
		flush();
		$this->i++;
	}

}
