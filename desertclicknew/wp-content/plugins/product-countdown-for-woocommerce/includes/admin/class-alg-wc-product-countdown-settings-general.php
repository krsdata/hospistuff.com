<?php
/**
 * Product Countdown for WooCommerce - General Section Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Product_Countdown_Settings_General' ) ) :

class Alg_WC_Product_Countdown_Settings_General extends Alg_WC_Product_Countdown_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'product-countdown-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * add_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_settings( $settings ) {
		$settings = array_merge(
			array(
				array(
					'title'     => __( 'Product Countdown Options', 'product-countdown-for-woocommerce' ),
					'type'      => 'title',
					'id'        => 'alg_wc_product_countdown_options',
				),
				array(
					'title'     => __( 'WooCommerce Product Countdown', 'product-countdown-for-woocommerce' ),
					'desc'      => '<strong>' . __( 'Enable', 'product-countdown-for-woocommerce' ) . '</strong>',
					'desc_tip'  => __( 'Product Countdown for WooCommerce.', 'product-countdown-for-woocommerce' ),
					'id'        => 'alg_wc_product_countdown_enabled',
					'default'   => 'yes',
					'type'      => 'checkbox',
				),
				array(
					'title'     => __( 'Format', 'product-countdown-for-woocommerce' ),
					'id'        => 'alg_wc_product_countdown_format',
					'default'   => '%s left',
					'type'      => 'text',
					'css'       => 'min-width:300px;',
				),
				array(
					'title'     => __( 'Style', 'product-countdown-for-woocommerce' ),
					'id'        => 'alg_wc_product_countdown_style',
					'default'   => 'font-size: xx-large; font-weight: bold;',
					'type'      => 'text',
					'css'       => 'min-width:300px;',
				),
				array(
					'type'      => 'sectionend',
					'id'        => 'alg_wc_product_countdown_options',
				),
			),
			$settings
		);
		return $settings;
	}

}

endif;

return new Alg_WC_Product_Countdown_Settings_General();
