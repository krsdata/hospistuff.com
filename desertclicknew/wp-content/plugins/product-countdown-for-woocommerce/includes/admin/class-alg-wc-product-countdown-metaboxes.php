<?php
/**
 * Product Countdown for WooCommerce - Metaboxes
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Product_Countdown_Metaboxes' ) ) :

class Alg_WC_Product_Countdown_Metaboxes {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_action( 'add_meta_boxes',    array( $this, 'add_counter_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_counter_meta_box' ), PHP_INT_MAX, 2 );
	}

	/**
	 * alg_get_table_html.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args = array_merge( $defaults, $args );
		extract( $args );
		$table_class = ( '' == $table_class ) ? '' : ' class="' . $table_class . '"';
		$table_style = ( '' == $table_style ) ? '' : ' style="' . $table_style . '"';
		$row_styles  = ( '' == $row_styles )  ? '' : ' style="' . $row_styles  . '"';
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr' . $row_styles . '>';
			foreach( $row as $column_number => $value ) {
				$th_or_td = ( ( 0 === $row_number && 'horizontal' === $table_heading_type ) || ( 0 === $column_number && 'vertical' === $table_heading_type ) ) ? 'th' : 'td';
				$column_class = ( ! empty( $columns_classes ) && isset( $columns_classes[ $column_number ] ) ) ? ' class="' . $columns_classes[ $column_number ] . '"' : '';
				$column_style = ( ! empty( $columns_styles ) && isset( $columns_styles[ $column_number ] ) ) ? ' style="' . $columns_styles[ $column_number ] . '"' : '';

				$html .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

	/**
	 * add_counter_metabox.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_counter_metabox() {
		add_meta_box(
			'alg-product-countdown',
			__( 'Product Countdown', 'product-countdown-for-woocommerce' ),
			array( $this, 'display_counter_metabox' ),
			'product',
			'side',
			'high'
		);
	}

	/**
	 * display_counter_metabox.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function display_counter_metabox() {
		$the_post_id      = get_the_ID();
		$is_enabled       = get_post_meta( $the_post_id, '_' . 'alg_product_countdown_enabled', true );
		$countdown_date   = get_post_meta( $the_post_id, '_' . 'alg_product_countdown_date',    true );
		$countdown_time   = get_post_meta( $the_post_id, '_' . 'alg_product_countdown_time',    true );
		$countdown_action = get_post_meta( $the_post_id, '_' . 'alg_product_countdown_action',    true );
		$table_data       = array();

		$field_html = '';
		$field_html .= '<select name="alg_product_countdown_enabled">';
		$field_html .= '<option value="no" '  . selected( 'no',  $is_enabled, false ) . '>' . __( 'No', 'product-countdown-for-woocommerce' )  . '</option>';
		$field_html .= '<option value="yes" ' . selected( 'yes', $is_enabled, false ) . '>' . __( 'Yes', 'product-countdown-for-woocommerce' ) . '</option>';
		$field_html .= '</select>';
		$table_data[] = array( __( 'Enabled', 'product-countdown-for-woocommerce' ), $field_html );

		$field_html = '';
		$field_html .= '<input type="text" name="alg_product_countdown_date" value="' . $countdown_date . '">' . '<br>' . '<em>YYYY-MM-DD</em>';
		$table_data[] = array( __( 'Date', 'product-countdown-for-woocommerce' ), $field_html );

		$field_html = '';
		$field_html .= '<input type="text" name="alg_product_countdown_time" value="' . $countdown_time . '">' . '<br>' . '<em>HH:MM</em>';
		$table_data[] = array( __( 'Time', 'product-countdown-for-woocommerce' ), $field_html );

		$field_html = '';
		$field_html .= '<select name="alg_product_countdown_action">';
		$field_html .= '<option value="do_nothing" '      . selected( 'do_nothing',      $countdown_action, false ) . '>' . __( 'Do nothing', 'product-countdown-for-woocommerce' )      . '</option>';
		$field_html .= '<option value="disable_product" ' . selected( 'disable_product', $countdown_action, false ) . '>' . __( 'Disable product', 'product-countdown-for-woocommerce' ) . '</option>';
		$field_html .= '</select>';
		$table_data[] = array( __( 'Action', 'product-countdown-for-woocommerce' ), $field_html );

		$html = '';
		$html .= $this->alg_get_table_html( $table_data, array( 'table_heading_type' => 'vertical', 'table_class' => 'widefat striped' ) );
		$html .= '<p><em>' . __( 'Current date and time', 'product-countdown-for-woocommerce' ) . ': ' . current_time( 'mysql' ) . '</em></p>';
		$html .= '<input type="hidden" name="alg_product_countdown_save_post" value="alg_product_countdown_save_post">';
		echo $html;
	}

	/**
	 * save_counter_meta_box.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save_counter_meta_box( $post_id, $post ) {
		// Check that we are saving with current metabox displayed.
		if ( ! isset( $_POST[ 'alg_product_countdown_save_post' ] ) ) {
			return;
		}
		update_post_meta( $post_id, '_' . 'alg_product_countdown_enabled', $_POST[ 'alg_product_countdown_enabled'] );
		update_post_meta( $post_id, '_' . 'alg_product_countdown_date',    $_POST[ 'alg_product_countdown_date'] );
		update_post_meta( $post_id, '_' . 'alg_product_countdown_time',    $_POST[ 'alg_product_countdown_time'] );
		update_post_meta( $post_id, '_' . 'alg_product_countdown_action',  $_POST[ 'alg_product_countdown_action'] );
	}

}

endif;

return new Alg_WC_Product_Countdown_Metaboxes();
