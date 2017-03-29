<?php

/**
 * Loop Add to Cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */
if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
global $product;
$add_to_cart = '';
if ( $product->is_purchasable() && $product->is_in_stock() ) {
	if ( get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes' ) {
		$add_to_cart = 'add_to_cart_button ajax_add_to_cart';
	} else {
		$add_to_cart = 'add_to_cart_button';
	}
}
echo apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>', esc_url( $product->add_to_cart_url() ), esc_attr( $product->id ), esc_attr( $product->get_sku() ), $add_to_cart, esc_attr( $product->product_type ), esc_html( $product->add_to_cart_text() )
		), $product );
