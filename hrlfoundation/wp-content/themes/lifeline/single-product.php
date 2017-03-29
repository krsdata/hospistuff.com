<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

sh_custom_header();
global $post_type;
$Settings = get_option( SH_NAME );
$PageSettings = get_post_meta( get_the_ID(), '_product_settings', true );
$IsWide = ( sh_set( $Settings, 'blog_layout' ) == 'wide' ) ? TRUE : FALSE;
$IsLeftSidebarLayout = ( sh_set( $Settings, 'blog_layout' ) == 'leftsidebar' ) ? TRUE : FALSE;
?>


<?php if ( sh_set( $PageSettings, 'top_image' ) ): ?>
	<div class="top-image"> <img src="<?php echo sh_set( $PageSettings, 'top_image' ); ?>" alt="" /></div>
<?php else: ?>
	<div class="no-top-image"></div>
<?php endif; ?>

<?php $Settings = get_option( SH_NAME ); ?>

<section class="inner-page<?php echo ( $IsLeftSidebarLayout ) ? 'switch' : ''; ?>">
    <div class="container">
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			<div class="page-title">
				<h1><?php echo get_the_title(); ?></h1>
			</div>
		<?php endif; ?>
        <div class="left-content col-md-12">
            <div id="post-<?php the_ID(); ?>" <?php post_class( "post" ); ?>>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php woocommerce_get_template_part( 'content', 'single-product' ); ?>
				<?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
<?php get_footer() ?>
