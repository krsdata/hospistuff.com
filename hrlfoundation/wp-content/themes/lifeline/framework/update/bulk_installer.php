<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );

if ( !class_exists( 'WP_Upgrader' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	if ( !class_exists( 'SH_Bulk_installer' ) ) {

		class SH_Bulk_installer extends WP_Upgrader {

			public $result;
			public $bulk = false;

			public function bulk_install( $packages ) {
				$this->init();
				$this->bulk = true;
				$this->install_strings();
				if ( SH_update_init::$instance->is_automatic )
					$this->activate_strings();

				$this->skin->header();
				$res = $this->fs_connect( array( WP_CONTENT_DIR, WP_PLUGIN_DIR ) );
				if ( !$res ) {
					$this->skin->footer();
					return false;
				}
				$this->skin->bulk_header();
				$results = array();
				$this->update_count = count( $packages );
				$this->update_current = 0;
				foreach ( $packages as $plugin ) {
					$this->update_current++;
					$result = $this->run(
							array(
								'package' => $plugin,
								'destination' => WP_PLUGIN_DIR,
								'clear_destination' => false,
								'clear_working' => true,
								'is_multi' => true,
								'hook_extra' => array( 'plugin' => $plugin, ),
							)
					);
					$results[$plugin] = $this->result;
					if ( false === $result )
						break;
				}
				$this->skin->bulk_footer();
				$this->skin->footer();
				return $results;
			}

			public function run( $options ) {
				$defaults = array(
					'package' => '',
					'destination' => '',
					'clear_destination' => false,
					'clear_working' => true,
					'is_multi' => false,
					'hook_extra' => array(),
				);
				$options = wp_parse_args( $options, $defaults );
				extract( $options );
				$res = $this->fs_connect( array( WP_CONTENT_DIR, $destination ) );
				if ( !$res )
					return false;

				if ( is_wp_error( $res ) ) {
					$this->skin->error( $res );
					return $res;
				}
				if ( !$is_multi )
					$this->skin->header();

				$this->skin->before();
				$download = $this->download_package( $package );
				if ( is_wp_error( $download ) ) {
					$this->skin->error( $download );
					$this->skin->after();
					return $download;
				}
				$delete_package = ( $download != $package );
				$working_dir = $this->unpack_package( $download, $delete_package );
				if ( is_wp_error( $working_dir ) ) {
					$this->skin->error( $working_dir );
					$this->skin->after();
					return $working_dir;
				}
				$result = $this->install_package(
						array(
							'source' => $working_dir,
							'destination' => $destination,
							'clear_destination' => $clear_destination,
							'clear_working' => $clear_working,
							'hook_extra' => $hook_extra,
						)
				);
				$this->skin->set_result( $result );
				if ( is_wp_error( $result ) ) {
					$this->skin->error( $result );
					$this->skin->feedback( 'process_failed' );
				} else {
					$this->skin->feedback( 'process_success' );
				}
				if ( SH_update_init::$instance->is_automatic ) {
					wp_cache_flush();
					$plugin_info = $this->plugin_info( $package );
					$activate = activate_plugin( $plugin_info );
					SH_update_init::$instance->populate_file_path();
					if ( is_wp_error( $activate ) ) {
						$this->skin->error( $activate );
						$this->skin->feedback( 'activation_failed' );
					} else {
						$this->skin->feedback( 'activation_success' );
					}
				}
				wp_cache_flush();
				$this->skin->after();
				if ( !$is_multi )
					$this->skin->footer();

				return $result;
			}

			public function install_strings() {
				$this->strings['no_package'] = __( 'Install package not available.', SH_NAME );
				$this->strings['downloading_package'] = __( 'Downloading install package from <span class="code">%s</span>&#8230;', SH_NAME );
				$this->strings['unpack_package'] = __( 'Unpacking the package&#8230;', SH_NAME );
				$this->strings['installing_package'] = __( 'Installing the plugin&#8230;', SH_NAME );
				$this->strings['process_failed'] = __( 'Plugin install failed.', SH_NAME );
				$this->strings['process_success'] = __( 'Plugin installed successfully.', SH_NAME );
			}

			public function activate_strings() {
				$this->strings['activation_failed'] = __( 'Plugin activation failed.', SH_NAME );
				$this->strings['activation_success'] = __( 'Plugin activated successfully.', SH_NAME );
			}

			public function plugin_info() {
				if ( !is_array( $this->result ) )
					return false;
				if ( empty( $this->result['destination_name'] ) )
					return false;

				$plugin = get_plugins( '/' . $this->result['destination_name'] );
				if ( empty( $plugin ) )
					return false;

				$pluginfiles = array_keys( $plugin );
				return $this->result['destination_name'] . '/' . $pluginfiles[0];
			}

		}

	}
}

