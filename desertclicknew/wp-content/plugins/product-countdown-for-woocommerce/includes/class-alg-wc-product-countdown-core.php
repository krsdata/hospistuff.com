<?php
/**
 * Product Countdown for WooCommerce - Core Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Product_Countdown_Core' ) ) :

class Alg_WC_Product_Countdown_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_wc_product_countdown_enabled', 'yes' ) ) {

			require_once( 'admin/class-alg-wc-product-countdown-metaboxes.php' );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );

			add_action( 'wp_ajax_alg_product_countdown',        array( $this, 'ajax_alg_product_countdown' ) );
			add_action( 'wp_ajax_nopriv_alg_product_countdown', array( $this, 'ajax_alg_product_countdown' ) );

			add_action( 'woocommerce_single_product_summary', array( $this, 'add_counter_to_frontend' ) );

			add_action( 'woocommerce_is_purchasable', array( $this, 'check_date' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * get_time_left_formatted.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_time_left_formatted( $product_id ) {
		if ( ( $result = $this->get_time_left( $product_id ) ) < 0 ) {
			return '';
		} else {
			$hours   = floor( $result / 3600 );
			$minutes = floor( ( $result / 60 ) % 60 );
			$seconds = $result % 60;
			$the_time = sprintf( '%02d:%02d:%02d', $hours, $minutes, $seconds ); // human_time_diff( 0, $result );
			return sprintf( get_option( 'alg_wc_product_countdown_format', '%s left' ), $the_time );
		}
	}

	/**
	 * get_time_left.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_time_left( $product_id ) {
		$finish_time =
			get_post_meta( $product_id, '_' . 'alg_product_countdown_date', true ) . ' ' .
			get_post_meta( $product_id, '_' . 'alg_product_countdown_time', true );
		$finish_time = strtotime( $finish_time );
		$current_time = (int) current_time( 'timestamp' );
		return ( $finish_time - $current_time );
	}

	/**
	 * ajax_alg_product_countdown.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function ajax_alg_product_countdown() {
		if ( 'yes' === get_post_meta( $_POST['product_id'], '_' . 'alg_product_countdown_enabled', true ) ) {
			echo $this->get_time_left_formatted( $_POST['product_id'] );
		}
		die();
	}

	/**
	 * check_date.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function check_date( $purchasable, $_product ) {
		if (
			'yes'             === get_post_meta( $_product->id, '_' . 'alg_product_countdown_enabled', true ) &&
			'disable_product' === get_post_meta( $_product->id, '_' . 'alg_product_countdown_action',  true ) &&
			$this->get_time_left( $_product->id ) < 0
		) {
			return false;
		}
		return $purchasable;
	}

	/**
	 * add_counter_to_frontend.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_counter_to_frontend() {
		$product_id = get_the_ID();
		if ( 'yes' === get_post_meta( $product_id, '_' . 'alg_product_countdown_enabled', true ) ) {
			echo '<span id="alg_product_countdown" product_id="' . $product_id . '" style="' . get_option( 'alg_wc_product_countdown_style', 'font-size: xx-large; font-weight: bold;' ). '"></span>';
		}
	}

	/**
	 * enqueue_styles_and_scripts.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function enqueue_styles_and_scripts() {
		if ( is_product() ) {
			$product_id = get_the_ID();
			if ( 'yes' === get_post_meta( $product_id, '_' . 'alg_product_countdown_enabled', true ) ) {
				wp_enqueue_script( 'simple-countdown-js',
					untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/js/simple-countdown.js',
					array( 'jquery' ),
					alg_wc_product_countdown()->version,
					true
				);
				wp_localize_script(
					'simple-countdown-js',
					'alg_data_countdown',
					array(
						'time_left'      => $this->get_time_left_formatted( $product_id ),
						'update_rate_ms' => 1000,
						'product_id'     => $product_id,
						'ajax_url'       => admin_url( 'admin-ajax.php' ),
					)
				);
			}
		}
	}

}

endif;

return new Alg_WC_Product_Countdown_Core();
