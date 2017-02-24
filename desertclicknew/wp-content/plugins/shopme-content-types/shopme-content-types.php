<?php
/*
Plugin Name: Shopme Custom Content Types and Taxonomies
Description: Content Types for Shopme eCommerce Theme.
Version: 1.0.4
Author: mad_velikorodnov
Author URI: inthe7heaven.com
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if (!class_exists('Shopme_Custom_Content_Types_and_Taxonomies')) {

	class Shopme_Custom_Content_Types_and_Taxonomies {

		public $paths = array();
		public static $view_path;

		public $content_types_classes = array(
			'Shopme_Testimonials_Config',
			'Shopme_Woo_Config'
		);

		function __construct() {

			// Plugin Activation/Deactivation
			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );

			// Load text domain
			add_action('plugins_loaded', array( &$this, 'load_textdomain' ) );

			$dir = plugin_dir_path(__FILE__);

			$this->paths = array(
				'APP_ROOT' => $dir,
				'APP_DIR' => basename( $dir ),
				'CLASSES_PATH' => $dir . 'classes/',
				'XML_PATH' => $dir . 'xml/'
			);

			self::$view_path = $this->paths;

			// Include classes
			$this->include_classes();

			// Register classes
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'init', array( &$this, 'init_classes' ) );
		}

		function admin_init() {

			if ( !class_exists('WXR_Parser') ) {
				require_once $this->paths['APP_ROOT'] . 'parsers.php';
			}

		}

		public function plugin_activation() {
			if ( version_compare( $GLOBALS['wp_version'], '4.0', '<' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
			}
			flush_rewrite_rules();
		}

		public function plugin_deactivation() {
			flush_rewrite_rules();
		}

		// include classes
		function include_classes() {
			foreach (glob($this->paths['CLASSES_PATH'] . '*.php') as $file) {
				require_once($file);
			}
		}

		// init classes
		function init_classes() {
			foreach ($this->content_types_classes as $content_type_class) {
				if (class_exists($content_type_class))
					new $content_type_class;
			}
		}

		// load plugin text domain
		function load_textdomain() {
			load_plugin_textdomain( 'shopme_app_textdomain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		// Get content type labels
		function getLabels($singular_name, $name, $title = FALSE) {
			if ( !$title )
				$title = $name;

			return array(
				"name" => $title,
				"singular_name" => $singular_name,
				"add_new" => __("Add New", 'shopme_app_textdomain'),
				"add_new_item" => sprintf( __("Add New %s", 'shopme_app_textdomain'), $singular_name),
				"edit_item" => sprintf( __("Edit %s", 'shopme_app_textdomain'), $singular_name),
				"new_item" => sprintf( __("New %s", 'shopme_app_textdomain'), $singular_name),
				"view_item" => sprintf( __("View %s", 'shopme_app_textdomain'), $singular_name),
				"search_items" => sprintf( __("Search %s", 'shopme_app_textdomain'), $name),
				"not_found" => sprintf( __("No %s found", 'shopme_app_textdomain'), $name),
				"not_found_in_trash" => sprintf( __("No %s found in Trash", 'shopme_app_textdomain'), $name),
				"parent_item_colon" => ""
			);
		}

		// Get content type taxonomy labels
		function getTaxonomyLabels($singular_name, $name) {
			return array(
				"name" => $name,
				"singular_name" => $singular_name,
				"search_items" => sprintf( __("Search %s", 'shopme_app_textdomain'), $name),
				"all_items" => sprintf( __("All %s", 'shopme_app_textdomain'), $name),
				"parent_item" => sprintf( __("Parent %s", 'shopme_app_textdomain'), $singular_name),
				"parent_item_colon" => sprintf( __("Parent %s:", 'shopme_app_textdomain'), $singular_name),
				"edit_item" => sprintf( __("Edit %", 'shopme_app_textdomain'), $singular_name),
				"update_item" => sprintf( __("Update %s", 'shopme_app_textdomain'), $singular_name),
				"add_new_item" => sprintf( __("Add New %s", 'shopme_app_textdomain'), $singular_name),
				"new_item_name" => sprintf( __("New %s Name", 'shopme_app_textdomain'), $singular_name),
				'not_found' => sprintf(__('No %s found', 'shopme_app_textdomain'), $singular_name),
				'not_found_in_trash' => sprintf(__('No %s found in Trash', 'shopme_app_textdomain'), $singular_name),
				"menu_name" => $name,
			);
		}

	}

	new Shopme_Custom_Content_Types_and_Taxonomies();

}
