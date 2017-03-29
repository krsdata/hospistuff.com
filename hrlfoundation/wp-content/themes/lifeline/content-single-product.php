<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

global $product;
?>
<?php
/**
 * woocommerce_before_single_product hook
 *
 * @hooked woocommerce_show_messages - 10
 */
do_action( 'woocommerce_before_single_product' );
?>
<div class="post">
    <div class="single-product-page">
        <div class="row">
            <div class="col-md-5">
				<?php woocommerce_get_template( 'single-product/sale-flash.php' ); ?>
				<?php the_post_thumbnail( '270x200' ); ?>
            </div>
            <div class="col-md-7">
                <h1><?php the_title(); ?></h1>
                <ul class="post-meta">
                    <li><?php the_date(); ?></li>
                    <li><?php woocommerce_get_template( 'single-product/meta.php' ); ?></li>
                    <li><?php echo $product->get_price_html(); ?></li>
                </ul>
                <div class="post-desc">
					<?php the_excerpt(); ?>
                </div>
				<?php do_action( 'woocommerce_' . $product->product_type . '_add_to_cart' ); ?>
            </div>
        </div>
    </div>
    <div class="post-desc">
		<?php woocommerce_get_template( 'single-product/share.php' ); ?>
    </div>
	<?php
	/**
	 * woocommerce_after_single_product_summary hook
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
