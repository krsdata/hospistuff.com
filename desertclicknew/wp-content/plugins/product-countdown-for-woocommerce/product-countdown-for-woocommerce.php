<?php
/*
Plugin Name: Product Countdown for WooCommerce
Description: Live WooCommerce product countdown.
Version: 1.0.0
Author: Algoritmika Ltd
Copyright: © 2016 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) {
	return;
}

if ( ! class_exists( 'Alg_WC_Product_Countdown' ) ) :

/**
 * Main Alg_WC_Product_Countdown Class
 *
 * @class   Alg_WC_Product_Countdown
 * @since   1.0.0
 * @version 1.0.0
 */

final class Alg_WC_Product_Countdown {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.0.0';

	/**
	 * @var   Alg_WC_Product_Countdown The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Product_Countdown Instance
	 *
	 * Ensures only one instance of Alg_WC_Product_Countdown is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_WC_Product_Countdown - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Product_Countdown Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @access  public
	 */
	function __construct() {

		// Include required files
		$this->includes();

		add_action( 'init', array( $this, 'init' ), 0 );

		// Settings & Scripts
		if ( is_admin() ) {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		}
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array( '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_product_countdown' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>' );
		return array_merge( $custom_links, $links );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function includes() {
		require_once( 'includes/admin/class-alg-wc-product-countdown-settings-section.php' );
		$settings = array();
		$settings[] = require_once( 'includes/admin/class-alg-wc-product-countdown-settings-general.php' );
		if ( is_admin() && get_option( 'alg_product_countdown_version', '' ) !== $this->version ) {
			foreach ( $settings as $section ) {
				foreach ( $section->get_settings() as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
			update_option( 'alg_product_countdown_version', $this->version );
		}
		require_once( 'includes/class-alg-wc-product-countdown-core.php' );
	}

	/**
	 * Add Product Countdown settings tab to WooCommerce settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = include( 'includes/admin/class-alg-wc-settings-product-countdown.php' );
		return $settings;
	}

	/**
	 * Init Alg_WC_Product_Countdown when WordPress initialises.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function init() {
		// Set up localisation
		load_plugin_textdomain( 'product-countdown-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

if ( ! function_exists( 'alg_wc_product_countdown' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Product_Countdown to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_Product_Countdown
	 */
	function alg_wc_product_countdown() {
		return Alg_WC_Product_Countdown::instance();
	}
}

alg_wc_product_countdown();
