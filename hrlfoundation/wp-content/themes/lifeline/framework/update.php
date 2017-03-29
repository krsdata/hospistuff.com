<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_update {

	public function __construct() {
		include_once(SH_ROOT . 'framework/modules/update_init.php');
		new SH_update_init();
		$plugins = new SH_Plugins();
		$this->duffer_plugin_module( $plugins->sh_plugin_list() );
		new SH_Update_notifier();
		new SH_Xml_importer();
		add_action( "wp_ajax_nopriv_theme-install-demo-data", array( $this, 'theme_install_demo_data' ) );
		add_action( "wp_ajax_theme-install-demo-data", array( $this, 'theme_install_demo_data' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'update_scripts' ) );
		add_action( "wp_ajax_nopriv_sh_update_theme", array( $this, 'sh_update_theme' ) );
		add_action( "wp_ajax_sh_update_theme", array( $this, 'sh_update_theme' ) );
	}

	public function theme_install_demo_data() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'theme-install-demo-data' ) {
			SH_Xml_importer::get_instance()->sh_demo_importer( $_POST );
			SH_Xml_importer::get_instance()->sh_remove_backup( ABSPATH . 'wp-content/themes/backup/backup/' );
			rmdir( ABSPATH . 'wp-content/themes/backup' );
			exit;
		}
	}

	public function update_scripts() {
		$scripts = array(
			'update' => 'js/update.js',
		);
		foreach ( $scripts as $key => $script ) {
			wp_register_script( $key, SH_URL . $script, array(), SH_VERSION, true );
		}
		wp_enqueue_script( array( 'update' ) );
	}

	public function duffer_plugin_module( $plugins, $config = array() ) {
		foreach ( $plugins as $plugin )
			SH_update_init::$instance->register( $plugin );

		if ( $config )
			SH_update_init::$instance->config( $config );
	}

	public function sh_update_theme() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'sh_update_theme' ) {
			SH_Update_theme::get_instance()->sh_update_theme_module();
			exit;
		}
	}

}
