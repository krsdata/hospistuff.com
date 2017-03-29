<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_Plugins {

	public function sh_plugin_list() {
		return array(
			array(
				'name' => 'Visual Composer',
				'slug' => 'js_composer',
				'source' => SH_ROOT . 'temp/js_composer.zip',
				'required' => true,
				'version' => '4.7',
				'force_activation' => false,
				'force_deactivation' => false,
				'external_url' => 'http://wpbakery.com/',
				'file_path' => ABSPATH . 'wp-content/plugins/js_composer/js_composer.php'
			),
			array(
				'name' => 'Layer Slider',
				'slug' => 'LayerSlider',
				'source' => SH_ROOT . 'temp/LayerSlider.zip',
				'required' => true,
				'version' => '5.5.1',
				'force_activation' => true,
				'force_deactivation' => true,
				'external_url' => 'http://codecanyon.net/user/kreatura/',
				'file_path' => ABSPATH . 'wp-content/plugins/LayerSlider/layerslider.php'
			),
			array(
				'name' => 'Revolution Slider',
				'slug' => 'revslider',
				'source' => SH_ROOT . 'temp/revslider.zip',
				'required' => true,
				'version' => '4.2.2',
				'force_activation' => false,
				'force_deactivation' => false,
				'external_url' => 'http://kreaturamedia.com/',
				'file_path' => ABSPATH . 'wp-content/plugins/revslider/revslider.php'
			),
			array(
				'name' => 'Woo Commerce WP',
				'slug' => 'woocommerce',
				'required' => true,
			),
		);
	}

}
