<?php
define( 'DOMAIN', 'wp_lifeline' );
define( 'SH_NAME', 'wp_lifeline' );
define( 'SH_VERSION', 'v3.0.1' );
define( 'SH_ROOT', get_template_directory() . '/' );
define( 'SH_URL', get_template_directory_uri() . '/' );
define( 'SH_DIR', dirname( __FILE__ ) );
define( 'TH_NAME', 'Lifeline' );
get_template_part( 'framework/loader' );
if ( in_array( 'bbpress/bbpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	get_template_part( 'framework/modules/bbpress_fix' );
}
define( 'STRIPE_PRIVATE_KEY', sh_set( get_option( SH_NAME ), 'credit_card_secret_key' ) );
define( 'STRIPE_PUBLIC_KEY', sh_set( get_option( SH_NAME ), 'credit_card_publish_key' ) );

add_action( 'after_setup_theme', 'sh_theme_setup' );
if ( !session_id() )
	session_start();

function sh_theme_setup() {
	global $wp_version;
//sh_create_donation_table();
	load_theme_textdomain( SH_NAME, get_template_directory() . '/languages' );
	add_editor_style();
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'menus' ); //Add menu support
	add_theme_support( 'automatic-feed-links' ); //Enables post and comment RSS feed links to head.
	add_theme_support( 'widgets' ); //Add widgets and sidebar support
	/** Register wp_nav_menus */
	add_theme_support( "custom-header" );
	add_theme_support( "custom-background" );
	add_theme_support( 'title-tag' );



	if ( function_exists( 'register_nav_menu' ) ) {
		register_nav_menus(
                    array(
                        /** Register Main Menu location header */
                        'main_menu' => __( 'Main Menu', SH_NAME ),
                        'footer_menu' => __( 'Footer Menu', SH_NAME ),
                        'responsive_menu' => __( 'Responsive Menu', SH_NAME ),
                    )
		);
	}
	if ( !isset( $content_width ) )
		$content_width = 960;
	$ThumbSize = array( '370x491', '1170x455', '370x252', '270x155', '570x570', '150x150', '570x184', '1170x312', '80x80', '470x318', '570x353' );
	foreach ( $ThumbSize as $v ) {
		$explode = explode( 'x', $v );
		add_image_size( $v, $explode[0], $explode[1], true );
	}
	if ( isset( $_POST['recurring_pp_submit'] ) ) {
		require_once(get_template_directory() . '/framework/modules/pp_recurring/expresscheckout.php');
	}
}

function sh_widget_init() {
	register_widget( 'SH_people_reviews' );
	register_widget( 'SH_Flickr' );
	register_widget( 'SH_Contact_Us' );
	register_widget( 'SH_News_Letter_Subscription' );
	register_widget( 'SH_Galleries' );
	register_widget( 'SH_Popular_Posts' );
	register_widget( 'SH_Recent_Events' );
	register_widget( 'SH_Video' );
	register_widget( 'SH_Donate_Us' );
	register_widget( 'sh_categories' );
	global $wp_registered_sidebars;
	register_sidebar( array(
		'name' => __( 'Default Sidebar', SH_NAME ),
		'id' => 'default-sidebar',
		'description' => __( 'Widgets in this area will be shown on the right-hand side.', SH_NAME ),
		'class' => '',
		'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="sidebar-title"><h4>',
		'after_title' => '</h4></div>'
	) );
	register_sidebar( array(
		'name' => __( 'Blog Listing', SH_NAME ),
		'id' => 'blog-sidebar',
		'description' => __( 'Widgets in this area will be shown on the right-hand side.', SH_NAME ),
		'class' => '',
		'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="sidebar-title"><h4>',
		'after_title' => '</h4></div>'
	) );
	register_sidebar( array(
		'name' => __( 'Footer Sidebar', SH_NAME ),
		'id' => 'footer-sidebar',
		'description' => __( 'Widgets in this area will be shown on the right-hand side.', SH_NAME ),
		'class' => 'quick-menu',
		'before_widget' => '<div class="col-md-3">',
		'after_widget' => '</div>',
		'before_title' => '<div class="footer-widget-title"><h4>',
		'after_title' => '</h4></div>'
	) );
	$sidebars = sh_set( get_option( SH_NAME ), 'dynamic_sidebars' ); //printr($sidebars);
	foreach ( array_filter( (array) $sidebars ) as $sidebar ) {
		register_sidebar( array(
			'name' => $sidebar,
			'id' => bistro_slug( $sidebar ),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => "</div>",
			'before_title' => '<div class="sidebar-title"><h4>',
			'after_title' => '</h4></div>',
		) );
	}
	update_option( 'wp_registered_sidebars', $wp_registered_sidebars );
}

add_action( 'widgets_init', 'sh_widget_init' );

function sh_custom_header() {
	$settings = get_option( SH_NAME );
	$HeaderName = ( sh_set( $settings, 'custom_header' ) !== 'dafault' ) ? sh_set( $settings, 'custom_header' ) : '';

//if(is_page(1160)): $HeaderName = NULL; $HeaderName = 'header-counter'; endif;
	get_header( $HeaderName );
}

function get_price_html( $price = '' ) {
	global $product;
// Ensure variation prices are synced with variations
	if ( $product->min_variation_price === '' || $product->min_variation_regular_price === '' || $product->price === '' )
//$product->variable_product_sync();
// Get the price
		if ( $product->price > 0 ) {
			if ( $product->is_on_sale() && isset( $product->min_variation_price ) && $product->min_variation_regular_price !== $product->get_price() ) {
				if ( !$product->min_variation_price || $product->min_variation_price !== $product->max_variation_price )
					$price .= $product->get_price_html_from_text();
				$price .= $product->get_price_html_from_to( $product->min_variation_regular_price, $product->get_price() );
				$price = apply_filters( 'woocommerce_variable_sale_price_html', $price, $product );
			}
			else {
				if ( $product->min_variation_price !== $product->max_variation_price )
					$price .= $product->get_price_html_from_text();
				$price .= woocommerce_price( $product->get_price() );
				$price = apply_filters( 'woocommerce_variable_price_html', $price, $product );
			}
		}
		elseif ( $product->price === '' ) {
			$price = apply_filters( 'woocommerce_variable_empty_price_html', '', $product );
		} elseif ( $product->price == 0 ) {
			if ( $product->is_on_sale() && isset( $product->min_variation_regular_price ) && $product->min_variation_regular_price !== $product->get_price() ) {
				if ( $product->min_variation_price !== $product->max_variation_price )
					$price .= $product->get_price_html_from_text();

				$price .= $product->get_price_html_from_to( $product->min_variation_regular_price, __( 'Free!', 'woocommerce' ) );

				$price = apply_filters( 'woocommerce_variable_free_sale_price_html', $price, $product );
			}
			else {
				if ( $product->min_variation_price !== $product->max_variation_price )
					$price .= $product->get_price_html_from_text();
				$price .= __( 'Free!', 'woocommerce' );
				$price = apply_filters( 'woocommerce_variable_free_price_html', $price, $product );
			}
		}
	return apply_filters( 'woocommerce_get_price_html', $price, $product );
}

function donation_box() {
	$paypal = $GLOBALS['_sh_base']->donation;
	echo '<div id="hidden_popup_donation_btn_click" style="display: none;"></div>';
	echo '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
	if ( isset( $_GET['recurring_pp_return'] ) && $_GET['recurring_pp_return'] == 'return' ) {
		$paypal_res = require_once(get_template_directory() . '/framework/modules/pp_recurring/review.php');
		?>
		<div class="donate-popup"><?php echo $paypal_res ?></div>
		<script>
		    jQuery(document).ready(function ($) {
		        var popup = jQuery("div.confirm_popup");
		        jQuery(popup).parent().css({"border-top": "none"});
		        jQuery('a[data-target="#myModal"]').trigger("click");
		    });
		</script>
		<?php
	}
	echo '</div>';
}

add_action( 'wp_ajax_theme-install-demo-data', 'theme_ajax_install_dummy_data' );

function theme_ajax_install_dummy_data() {
	require_once('framework/helpers/importer.php');
	sh_xml_importer();
	die();
}


remove_filter( 'nav_menu_description', 'strip_tags' );

function sh_setup_nav_menu_item( $menu_item ) {
	if ( isset( $menu_item->post_type ) ) {
		if ( 'nav_menu_item' == $menu_item->post_type ) {
			$menu_item->description = apply_filters( 'nav_menu_description', $menu_item->post_content );
		}
	}

	return $menu_item;
}

add_filter( 'wp_setup_nav_menu_item', 'sh_setup_nav_menu_item' );

//responsive menu
function sh_responsive_menu() {
    $settings = get_option( SH_NAME );
    wp_enqueue_script(array('perfect-scrollbar-jquery','perfect-scrollbar'));
    ?>
    <div class="responsive-header">
        <?php if(sh_set($settings, 'sh_responsive_header_top_bar')):?>
            <div class="responsive-topbar">		
                <div class="responsive-topbar-info">
                    <ul>
                        <?php echo (sh_set($settings, 'responsive_header_address')) ? '<li><i class="icon-home"></i> '.sh_set($settings, 'responsive_header_address').'</li>' : '';?>
                        <?php echo (sh_set($settings, 'responsive_header_phone_number')) ? '<li><i class="icon-phone"></i> '.sh_set($settings, 'responsive_header_phone_number').'</li>' : '';?>
                        <?php echo (sh_set($settings, 'responsive_header_email_address')) ? '<li><i class="icon-envelope"></i> '.sh_set($settings, 'responsive_header_email_address').'</li>' : '';?>
                    </ul>
                    <?php if ( sh_set( $settings, 'sh_show_responsive_soical_icons' ) == 'true' ): ?>
                    <div class="container">
                        <div class="responsive-socialbtns">
                            <ul>
                                <?php echo ( sh_set($settings, 'contact_rss') ) ? '<li><a target="_blank" href="' . sh_set($settings, 'contact_rss') . '" title=""><i class="icon-rss"></i></a></li>' : ''; ?>
                                <?php echo ( sh_set($settings, 'contact_gplus') ) ? '<li><a target="_blank" href="' . sh_set($settings, 'contact_gplus') . '" title=""><i class="icon-google-plus"></i></a></li>' : ''; ?>
                                <?php echo ( sh_set($settings, 'contact_facebook') ) ? '<li><a target="_blank" href="' . sh_set($settings, 'contact_facebook') . '" title=""><i class="icon-facebook"></i></a></li>' : ''; ?>
                                <?php echo ( sh_set($settings, 'contact_twitter') ) ? '<li><a target="_blank" href="' . sh_set($settings, 'contact_twitter') . '" title=""><i class="icon-twitter"></i></a></li>' : ''; ?>
                                <?php echo ( sh_set($settings, 'contact_linkedin') ) ? '<li><a target="_blank" href="' . sh_set($settings, 'contact_linkedin') . '" title=""><i class="icon-linkedin"></i></a></li>' : ''; ?>
                                <?php echo ( sh_set($settings, 'contact_pintrest') ) ? '<li><a target="_blank" href="' . sh_set($settings, 'contact_pintrest') . '" title=""><i class="icon-pinterest"></i></a></li>' : ''; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        <?php endif;?>
	<div class="responsive-logomenu">
            <div class="container">
                <?php
                if ( sh_set( $settings, 'responsive_logo_text_status' ) === 'true' ) {
                        $LogoStyle = sh_get_font_settings(
                            array( 'respnosive_logo_text_font_size' => 'font-size',
                                'responsive_logo_text_font_family' => 'font-family',
                                'responsive_logo_text_font_style' => 'font-style',
                                'responsive_logo_text_color' => 'color' ), ' style="', '"' );
                        $Logo = $settings['responsive_logo_text'];
                } else {
                    $LogoStyle = '';
                    $LogoImageStyle = ( sh_set( $settings, 'responsive_logo_width' ) || sh_set( $settings, 'responsive_logo_height' ) ) ? ' style="' : '';
                    $LogoImageStyle .= ( sh_set( $settings, 'responsive_logo_width' ) ) ? ' width:' . sh_set( $settings, 'responsive_logo_width' ) . 'px;' : '';
                    $LogoImageStyle .= ( sh_set( $settings, 'responsive_logo_height' ) ) ? ' height:' . sh_set( $settings, 'responsive_logo_height' ) . 'px;' : '';
                    $LogoImageStyle .= ( sh_set( $settings, 'responsive_logo_width' ) || sh_set( $settings, 'responsive_logo_height' ) ) ? '"' : '';
                    $Logo = '<img src="' . sh_set( $settings, 'responsive_logo_image' ) . '" alt=""' . $LogoImageStyle . ' />';
                }
                ?>
                <a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>"<?php echo $LogoStyle; ?>>
                    <?php if ( sh_set( $settings, 'responsive_logo_text_status' ) === 'true' )  ?> <h1 <?php echo $LogoStyle; ?>>
                    <?php echo $Logo; ?>
                    <?php if ( sh_set( $settings, 'responsive_logo_text_status' ) === 'true' )  ?> </h1>
                </a>
                <?php
                if ( sh_set( $settings, 'responsive_logo_text_status' ) === 'true' && sh_set( $settings, 'responsive_site_salogan' ) ) {
                    $SaloganStyle = sh_get_font_settings( array( 'responsive_salogan_font_size' => 'font-size', 'responsive_salogan_font_family' => 'font-family', 'responsive_salogan_font_style' => 'font-style' ), ' style="', '"' );
                    echo '<p' . $SaloganStyle . '>' . sh_set( $settings, 'responsive_site_salogan' ) . '</p>';
                }
                ?>
                <span class="menu-btn"><i class="icon-list"></i></span>
            </div>
	</div>
	<div class="responsive-menu">
		<span class="close-btn"><i class="fa fa-close"></i></span>
                <?php wp_nav_menu( array( 'theme_location' => 'responsive_menu', 'menu_class' => '', 'container' => null) ); ?>
	</div> 
        <?php if ( sh_set( $settings, 'sh_show_responsive_donate_btn' ) == 'true' ): ?>
                <a data-toggle="modal" data-target="#myModal" data-url="<?php echo get_permalink() ?>" data-type="general" class="btn-don responsive-donate" href="#" title=""><?php echo sh_set( $settings, 'sh_show_donate_btn_txt' ) ?></a>
        <?php endif; ?>
	
</div><!--Responsive header-->  
    
	<?php
}

function sh_woo_pages( $page_id ) {
	$pages = array(
		get_option( 'woocommerce_shop_page_id' ),
		get_option( 'woocommerce_cart_page_id' ),
		get_option( 'woocommerce_checkout_page_id' ),
		get_option( 'woocommerce_pay_page_id' ),
		get_option( 'woocommerce_thanks_page_id' ),
		get_option( 'woocommerce_myaccount_page_id' ),
		get_option( 'woocommerce_edit_address_page_id' ),
		get_option( 'woocommerce_view_order_page_id' ),
		get_option( 'woocommerce_terms_page_id' )
	);
	return ( in_array( $page_id, $pages ) ) ? 'true' : 'false';
}

function sh_search_filter( $query ) {
	if ( !$query->is_admin && $query->is_search ) {
		$query->set( 'post_type', array( 'post', 'dict_testimonials', 'dict_causes', 'dict_project', 'dict_event', 'dict_portfolio', 'dict_gallery', 'dict_team', 'dict_services' ) );
	}
	return $query;
}

add_filter( 'pre_get_posts', 'sh_search_filter' );

function admin_donwload_pdf() {
	if ( $_POST['action'] == 'admin_donwload_pdf' ) {
		$data_id = sh_set( $_POST, 'data_id' );
		$transaction_array = get_option( 'general_donation' );
		$settings = get_option( SH_NAME );
		$user_ID = get_current_user_id();
		$img = sh_set( $settings, 'logo_image' );
		$user = get_userdata( $user_ID );
		require('pdf/fpdf.php');
		$pdf = new FPDF();
		$pdf->AddPage();
		if ( !empty( $img ) ): $pdf->Image( $img, 70, 7, 61, 15 );
		endif;
		$pdf->SetDrawColor( 177, 218, 227 ); // Hot Pink
		$pdf->Line( 208, 25, 2, 25 );
		$pdf->SetAutoPageBreak( true, 0 );
		$pdf->AliasNbPages();
		$pdf->SetFont( 'helvetica', 'B', '10' );

		$pdf->Ln( 25 );
		$pdf->Cell( 100, 0, __( 'Name:', SH_NAME ) );
		$pdf->Cell( 100, 0, $user->user_nicename );
		$pdf->Ln( 2 );
		$pdf->SetDrawColor( 177, 218, 227 );
		$pdf->SetLineWidth( 1 );
		$pdf->Rect( 2, 40, 206, 80, 'D' );
		if ( !empty( $transaction_array ) ) {
			foreach ( $transaction_array as $trasaction ):
				if ( in_array( $data_id, $trasaction ) ) {
					$pdf->MultiCell( 150, 20, __( 'Transacction ID:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -20, sh_set( $trasaction, 'transaction_id' ), 0, 'R' );

					$pdf->MultiCell( 150, 30, __( 'Transacction Type:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -30, sh_set( $trasaction, 'transaction_type' ), 0, 'R' );

					$pdf->MultiCell( 150, 40, __( 'Payment Type:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -40, sh_set( $trasaction, 'payment_type' ), 0, 'R' );

					$pdf->MultiCell( 150, 50, __( 'Order Time:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -50, sh_set( $trasaction, 'order_time' ), 0, 'R' );

					$pdf->MultiCell( 150, 60, __( 'Amount:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -60, sh_set( $trasaction, 'amount' ), 0, 'R' );

					$pdf->MultiCell( 150, 70, __( 'Currency Code:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -70, sh_set( $trasaction, 'currency_code' ), 0, 'R' );

					$pdf->MultiCell( 150, 80, __( 'Fee Amount:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -80, sh_set( $trasaction, 'fee_amount' ), 0, 'R' );

					$pdf->MultiCell( 150, 90, __( 'Settle Amount:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -90, sh_set( $trasaction, 'settle_amount' ), 0, 'R' );

					$pdf->MultiCell( 150, 100, __( 'Tax Amount:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -100, sh_set( $trasaction, 'tax_amount' ), 0, 'R' );

					$pdf->MultiCell( 150, 110, __( 'Exchange Rate:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -110, sh_set( $trasaction, 'exchange_rate' ), 0, 'R' );

					$pdf->MultiCell( 150, 120, __( 'Payment Status:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -120, sh_set( $trasaction, 'payment_status' ), 0, 'R' );

					$pdf->MultiCell( 150, 130, __( 'Pendign Reason:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -130, sh_set( $trasaction, 'pending_reason' ), 0, 'R' );

					$pdf->MultiCell( 150, 140, __( 'Reason Code:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -140, sh_set( $trasaction, 'reason_code' ), 0, 'R' );

					$pdf->MultiCell( 150, 150, __( 'Donation Type:', SH_NAME ), 0, 'L' );
					$pdf->MultiCell( 150, -150, sh_set( $trasaction, 'donation_type' ), 0, 'R' );
				}
			endforeach;
		}
		$pdf->Output( SH_ROOT . $user_ID . '_filename.pdf', 'F' );
		die();
	}
}

add_action( 'wp_ajax_admin_donwload_pdf', 'admin_donwload_pdf' );
add_action( 'wp_ajax_nopriv_admin_donwload_pdf', 'admin_donwload_pdf' );

function vp_get_posts_custom( $post_tyep ) {
	$args = array(
		'post_type' => $post_tyep,
		'post_status' => 'publish',
		'posts_per_page' => -1,
	);

	$result = array();
	$my_query = null;
	$my_query = new WP_Query( $args );
	if ( $my_query->have_posts() ) {
		foreach ( $my_query->posts as $key => $value ):
			$result[$value->ID] = $value->post_title;
		endforeach;
	}
	return $result;
	wp_reset_query();
}

add_action( 'wp_ajax_sh_credit_card_process', 'sh_credit_card_process_' );
add_action( 'wp_ajax_nopriv_sh_credit_card_process', 'sh_credit_card_process_' );

function sh_credit_card_process_() {
	$errors = array();
	if ( sh_set( $_POST, 'token' ) ) {
		$token = $_POST['token'];
		if ( isset( $_SESSION['token'] ) && ($_SESSION['token'] == $token) ) {
			$errors['token'] = __( 'You have apparently resubmitted the form. Please do not do that.', SH_NAME );
		} else {
			$_SESSION['token'] = $token;
		}
	} else {
		$errors['token'] = __( 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.', SH_NAME );
	}

	$amount = sh_set( $_POST, 'amount' );
	if ( empty( $errors ) ) {
		try {
			require_once(SH_ROOT . 'framework/library/credit_card/stripe.php');
			$sh_currency_code = sh_set( $_POST, 'currency', 'USD' );
			Stripe\Stripe::setApiKey( STRIPE_PRIVATE_KEY );
			$charge = Stripe\Charge::create( array(
						"amount" => bcmul( $amount, 100 ),
						"currency" => strtolower( $sh_currency_code ),
						"source" => $token,
							//"description" => $email
							)
			);
			if ( $charge->paid ) {
				$new = $amount;
				$settings['paypal_raised'] = $new;
				if ( isset( $_SESSION['donation_type_popup'] ) && $_SESSION['donation_type_popup'] == 'dict_causes' ) {
					$id = $_SESSION['donation_id_popup'];
					$cause_donation = array();
					$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
					array_push(
							$cause_donation, array(
						'donner_name' => sh_set( $_POST, 'don_name' ),
						'donner_email' => sh_set( $_POST, 'don_email' ),
						'transaction_id' => $charge->id,
						'transaction_type' => $charge->source->brand,
						'payment_type' => $charge->source->funding,
						'order_time' => date( 'c', $charge->created ),
						'amount' => $amount,
						'currency_code' => strtoupper( $charge->currency ),
						'fee_amount' => '',
						'settle_amount' => $amount,
						'payment_status' => $charge->status,
						'pending_reason' => '',
						'payer_id' => $charge->source->id,
						'ship_to_name' => '',
						'donation_type' => __( 'Single', SH_NAME ),
							)
					);
					$get_old = get_post_meta( $id, '_dict_causes_settings', true );
					$c_collect_ = (sh_set( $get_old, 'donation_collected' )) ? sh_set( $get_old, 'donation_collected' ) : 0;
					$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
					foreach ( $get_old as $k => $o ) {
						if ( $k == 'donation_collected' ) {
							$get_old['donation_collected'] = number_format( $updated );
						}
					}
					update_post_meta( $id, '_dict_causes_settings', $get_old );
					update_post_meta( $id, 'single_causes_donation', $cause_donation );
					unset( $_SESSION['donation_type_popup'] );
					unset( $_SESSION['donation_id_popup'] );
				} elseif ( isset( $_SESSION['donation_type_popup'] ) && $_SESSION['donation_type_popup'] == 'dict_project' ) {
					$id = $_SESSION['donation_id_popup'];
					$cause_donation = array();
					$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
					array_push(
							$cause_donation, array(
						'donner_name' => sh_set( $_POST, 'don_name' ),
						'donner_email' => sh_set( $_POST, 'don_email' ),
						'transaction_id' => $charge->id,
						'transaction_type' => $charge->source->brand,
						'payment_type' => $charge->source->funding,
						'order_time' => date( 'c', $charge->created ),
						'amount' => $amount,
						'currency_code' => strtoupper( $charge->currency ),
						'fee_amount' => '',
						'settle_amount' => $amount,
						'payment_status' => $charge->status,
						'pending_reason' => '',
						'payer_id' => $charge->source->id,
						'ship_to_name' => '',
						'donation_type' => __( 'Single', SH_NAME ),
							)
					);
					$get_old = get_post_meta( $id, '_dict_project_settings', true );
					$c_collect_ = (sh_set( $get_old, 'amount_needed' )) ? sh_set( $get_old, 'amount_needed' ) : 0;
					$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
					foreach ( $get_old as $k => $o ) {
						if ( $k == 'amount_needed' ) {
							$get_old['amount_needed'] = number_format( $updated );
						}
					}
					update_post_meta( $id, '_dict_project_settings', $get_old );
					update_post_meta( $id, 'single_causes_donation', $cause_donation );
					unset( $_SESSION['donation_type_popup'] );
					unset( $_SESSION['donation_id_popup'] );
				} else {
					$cause_donation = array();
					$cause_donation = (get_option( 'general_donation' )) ? get_option( 'general_donation' ) : array();
					array_push(
							$cause_donation, array(
						'donner_name' => sh_set( $_POST, 'don_name' ),
						'donner_email' => sh_set( $_POST, 'don_email' ),
						'transaction_id' => $charge->id,
						'transaction_type' => $charge->source->brand,
						'payment_type' => $charge->source->funding,
						'order_time' => date( 'c', $charge->created ),
						'amount' => $amount,
						'currency_code' => strtoupper( $charge->currency ),
						'fee_amount' => '',
						'settle_amount' => $amount,
						'payment_status' => $charge->status,
						'pending_reason' => '',
						'payer_id' => $charge->source->id,
						'ship_to_name' => '',
						'donation_type' => __( 'Single', SH_NAME ),
							)
					);

					$donation_data = get_option( SH_NAME );
					$c_collect_ = (sh_set( $donation_data, 'paypal_raised' )) ? sh_set( $donation_data, 'paypal_raised' ) : 0;
					$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
					foreach ( $donation_data as $k => $o ) {
						if ( $k == 'paypal_raised' ) {
							$donation_data['paypal_raised'] = number_format( $updated );
						}
					}
					update_option( SH_NAME, $donation_data );
					update_option( 'general_donation', $cause_donation );
				}
				echo __( 'Payment Made Successfully!', SH_NAME );
			} else {
				echo __( 'Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction. You can try again or use another card.', SH_NAME );
			}
		} catch ( Stripe\Error\Card $e ) {
			$e_json = $e->getJsonBody();
			$err = $e_json['error'];
			$errors['stripe'] = $err['message'];
		} catch ( Stripe\Error\ApiConnection $e ) {
			$e_json = $e->getJsonBody();
			$err = $e_json['error'];
			$errors['stripe'] = $err['message'];
		} catch ( Stripe\Error\InvalidRequest $e ) {
			$e_json = $e->getJsonBody();
			$err = $e_json['error'];
			$errors['stripe'] = $err['message'];
		} catch ( Stripe\Error\Api $e ) {
			$e_json = $e->getJsonBody();
			$err = $e_json['error'];
			$errors['stripe'] = $err['message'];
		} catch ( Stripe\Error\Base $e ) {
			$e_json = $e->getJsonBody();
			$err = $e_json['error'];
			$errors['stripe'] = $err['message'];
		}
	} else {
		if ( isset( $errors ) && !empty( $errors ) && is_array( $errors ) ) {
			foreach ( $errors as $e ) {
				echo $e . "\n\n";
			}
		}
	}
	exit;
}

add_action( 'wp_ajax_sh_donation_popup_ajax', 'sh_donation_popup_ajax' );
add_action( 'wp_ajax_nopriv_sh_donation_popup_ajax', 'sh_donation_popup_ajax' );

function sh_donation_popup_ajax() {
	if ( isset( $_POST ) && sh_set( $_POST, 'action' ) == 'sh_donation_popup_ajax' ) {
		ob_start();
		if ( isset( $_SESSION['donation_type_popup'] ) ) {
			unset( $_SESSION['donation_type_popup'] );
		} elseif ( isset( $_SESSION['donation_id_popup'] ) ) {
			unset( $_SESSION['donation_id_popup'] );
		}
		if ( !session_id() )
			session_start();
		$r = session_id();
		$paypal = $GLOBALS['_sh_base']->donation;
		$http = (is_ssl()) ? 'https' : 'http';
		$return_url = 'http://localhost/one_click_importer/paypal-ipn/';
		$redirect_url = $_SESSION['redirect_uri'] = sh_set( $_POST, 'url' );
		$temp_array = array( 'type' => sh_set( $_POST, 'types' ), 'id' => sh_set( $_POST, 'id' ), 'redirect' => $redirect_url );
		update_option( 'temp_donation_recuring' . $r, $temp_array );

		if ( sh_set( $_POST, 'types' ) == 'post' ) {

			$_SESSION['donation_id_popup'] = sh_set( $_POST, 'id' );
			$_SESSION['donation_type_popup'] = 'dict_causes';

			$args = array(
				'post_type' => "dict_causes",
				'p' => sh_set( $_POST, 'id' ),
			);
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$settings = get_post_meta( get_the_ID(), '_dict_causes_settings', true );
					$symbol = sh_set( $settings, 'currency_symbol', '$' );
					$sh_currency_code = sh_set( $settings, 'currency_code', 'USD' );
					$c_needed = sh_set( $settings, 'donation_needed' );
					$c_collect = sh_set( $settings, 'donation_collected' );
					$c_percent = ($c_needed) ? (int) str_replace( ',', '', $c_collect ) / (int) str_replace( ',', '', $c_needed ) : 0;
					$c_donation_percentage = round( $c_percent * 100, 2, PHP_ROUND_HALF_UP );
				}
			}
		} else if ( sh_set( $_POST, 'types' ) == 'project' ) {
			$_SESSION['donation_id_popup'] = sh_set( $_POST, 'id' );
			$_SESSION['donation_type_popup'] = 'dict_project';

			$args = array(
				'post_type' => "dict_project",
				'p' => sh_set( $_POST, 'id' ),
			);
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$settings = get_post_meta( get_the_ID(), '_dict_project_settings', true );
					$symbol = sh_set( $settings, 'currency_symbol', '$' );
					$sh_currency_code = sh_set( $settings, 'currency_code', 'USD' );
					$p_needed = sh_set( $settings, 'amount_needed' );
					$p_collect = sh_set( $settings, 'spent_amount' );
					$p_percent = ($p_needed) ? (int) str_replace( ',', '', $p_needed ) / (int) str_replace( ',', '', $p_collect ) : 0;
					$p_donation_percentage = round( $p_percent * 100, 2, PHP_ROUND_HALF_UP );
				}
			}
		} else {
			$donation_data = get_option( SH_NAME );
			$symbol = (sh_set( $donation_data, 'paypal_currency' )) ? sh_set( $donation_data, 'paypal_currency' ) : '$';
			$percent = (sh_set( $donation_data, 'paypal_target' )) ? (int) str_replace( ',', '', sh_set( $donation_data, 'paypal_raised' ) ) / (int) str_replace( ',', '', sh_set( $donation_data, 'paypal_target' ) ) : 0;
			$donation_percentage = $percent * 100;
			$settings = get_option( SH_NAME );
			$sh_currency_code = sh_set( $settings, 'currency_code', 'USD' );
		}

		$Settings = get_option( SH_NAME );
		$value = sh_set( $Settings, 'transactions_detail' );
		?>
		<div class="donate-popup">
			<?php if ( sh_set( $_POST, 'types' ) == 'project' ): ?>
				<div class="cause-bar">
					<div class="cause-box">
						<h3>
							<span><?php echo $symbol ?></span>
							<?php echo $p_needed ?>
						</h3>
						<i><?php _e( 'NEEDED DONATION', SH_NAME ) ?></i>
					</div>
					<div class="cause-progress">
						<div class="progress-report">
							<h6><?php _e( 'PHASES', SH_NAME ) ?></h6>
							<span><?php echo $p_donation_percentage ?>%</span>
							<div class="progress pattern">
								<div class="progress-bar" style="width: <?php echo $p_donation_percentage ?>%"></div>
							</div>
						</div>
					</div>
					<div class="cause-box">
						<h3>
							<span><?php echo $symbol ?></span>
							<?php echo $p_collect ?></h3>
						<i><?php _e( 'COLLECTED DONATION', SH_NAME ) ?></i>
					</div>
					<div class="cause-box donate-drop-btn">
						<h4><?php _e( 'DONATE NOW', SH_NAME ) ?></h4>
					</div>
				</div>
			<?php elseif ( sh_set( $_POST, 'types' ) == 'post' ): ?>
				<div class="cause-bar">
					<div class="cause-box">
						<h3>
							<span><?php echo $symbol ?></span>
							<?php echo $c_needed ?>
						</h3>
						<i><?php _e( 'NEEDED DONATION', SH_NAME ) ?></i>
					</div>
					<div class="cause-progress">
						<div class="progress-report">
							<h6><?php _e( 'PHASES', SH_NAME ) ?></h6>
							<span><?php echo $c_donation_percentage ?>%</span>
							<div class="progress pattern">
								<div class="progress-bar" style="width: <?php echo $c_donation_percentage ?>%"></div>
							</div>
						</div>
					</div>
					<div class="cause-box">
						<h3>
							<span><?php echo $symbol ?></span>
							<?php echo $c_collect ?></h3>
						<i><?php _e( 'COLLECTED DONATION', SH_NAME ) ?></i>
					</div>
					<div class="cause-box donate-drop-btn">
						<h4><?php _e( 'DONATE NOW', SH_NAME ) ?></h4>
					</div>
				</div>
			<?php else: ?>
				<div class="cause-bar">
					<div class="cause-box">
						<h3>
							<span><?php echo $symbol ?></span>
							<?php echo sh_set( $donation_data, 'paypal_target' ) ?>
						</h3>
						<i><?php _e( 'NEEDED DONATION', SH_NAME ) ?></i>
					</div>
					<div class="cause-progress">
						<div class="progress-report">
							<h6><?php _e( 'PHASES', SH_NAME ) ?></h6>
							<span><?php echo $donation_percentage ?>%</span>
							<div class="progress pattern">
								<div class="progress-bar" style="width: <?php echo $donation_percentage ?>%"></div>
							</div>
						</div>
					</div>
					<div class="cause-box">
						<h3>
							<span><?php echo $symbol ?></span>
							<?php echo sh_set( $donation_data, 'paypal_raised' ) ?></h3>
						<i><?php _e( 'COLLECTED DONATION', SH_NAME ) ?></i>
					</div>
					<div class="cause-box donate-drop-btn">
						<h4><?php _e( 'DONATE NOW', SH_NAME ) ?></h4>
					</div>
				</div>
			<?php endif; ?>
			<div class="donate-drop-down">
				<div class="recursive-periods" align="center">
					<?php
					$translated = array( 
						'One Time' => __( 'One Time', SH_NAME ), 
						'daily' => __( 'Daily', SH_NAME ), 
						'weekly' => __( 'Weekly', SH_NAME ), 
						'fortnightly' => __( 'Fortnightly', SH_NAME ), 
						'monthly' => __( 'Monthly', SH_NAME ), 
						'quarterly' => __( 'Quarterly', SH_NAME ), 
						'half_year' => __( 'Half Year', SH_NAME ), 
						'yearly' => __( 'Yearly', SH_NAME ), 
					);
					if ( $value ) {
						foreach ( $value as $val ) {
								echo '<a data-symbol="' . $sh_currency_code . '" data-currency="' . $symbol . '" style="cursor:pointer;">' . $translated[$val]. '</a>';
							
						}
					}
					?>
				</div>
				<div class="amount-btns">
					<?php
					if ( intval( sh_set( $Settings, 'pop_up_1st_value' ) ) != '' )
						echo '<a style="cursor:pointer;">' . $symbol . '<span>' . sh_set( $Settings, 'pop_up_1st_value' ) . '</span></a>';
					if ( intval( sh_set( $Settings, 'pop_up_2nd_value' ) ) != '' )
						echo '<a style="cursor:pointer;">' . $symbol . '<span>' . sh_set( $Settings, 'pop_up_2nd_value' ) . '</span></a>';
					if ( intval( sh_set( $Settings, 'pop_up_3rd_value' ) ) != '' )
						echo '<a style="cursor:pointer;">' . $symbol . '<span>' . sh_set( $Settings, 'pop_up_3rd_value' ) . '</span></a>';
					if ( intval( sh_set( $Settings, 'pop_up_4th_value' ) ) != '' )
						echo '<a style="cursor:pointer;">' . $symbol . '<span>' . sh_set( $Settings, 'pop_up_4th_value' ) . '</span></a>';
					if ( intval( sh_set( $Settings, 'pop_up_5th_value' ) ) != '' )
						echo '<a style="cursor:pointer;">' . $symbol . '<span>' . sh_set( $Settings, 'pop_up_5th_value' ) . '</span></a>';
					if ( intval( sh_set( $Settings, 'pop_up_6th_value' ) ) != '' )
						echo '<a style="cursor:pointer;">' . $symbol . '<span>' . sh_set( $Settings, 'pop_up_6th_value' ) . '</span></a>';
					if ( intval( sh_set( $Settings, 'pop_up_7th_value' ) ) != '' )
						echo '<a style="cursor:pointer;">' . $symbol . '<span>' . sh_set( $Settings, 'pop_up_7th_value' ) . '</span></a>';				
					?>
				</div>
				<div class="payment-method">
					<div class="payment-choices">
						<?php if ( sh_set( get_option( SH_NAME ), 'enable_paypal' ) == 'true' ): ?><a class="paypal-donation" href="javascript:void(0)" title=""><?php _e( 'PAYPAL', SH_NAME ) ?></a><?php endif; ?>
						<?php if ( sh_set( get_option( SH_NAME ), 'enable_stripe' ) == 'true' ): ?><a class="credit-card" href="javascript:void(0)" title=""><?php _e( 'CREDIT CARD', SH_NAME ) ?></a><?php endif; ?>
						<?php if ( sh_set( get_option( SH_NAME ), 'enable_checkout2' ) == 'true' ): ?><a class="checkout2" href="javascript:void(0)" title=""><?php _e( '2Checkout', SH_NAME ) ?></a><?php endif; ?>
						<?php if ( sh_set( get_option( SH_NAME ), 'enable_braintree' ) == 'true' ): ?><a class="braintree" href="javascript:void(0)" title=""><?php _e( 'Braintree', SH_NAME ) ?></a><?php endif; ?>
					</div>
					<?php if ( sh_set( get_option( SH_NAME ), 'enable_paypal' ) == 'true' || sh_set( get_option( SH_NAME ), 'enable_stripe' ) == 'true' ): ?>
						<div class="other-amount donner credit-card-options">
							<div id="sh_to_errors" class="col-md-12">
							</div>
							<div class="row">
								<div class="col-md-6">
									<input id="donner_name" name="donner_name" type="text" placeholder="<?php _e( 'ENTER YOUR NAME PLEASE', SH_NAME ); ?>" autocomplete="off" />
								</div>

								<div class="col-md-6">
									<input id="donner_email" name="donner_email" type="text" placeholder="<?php _e( 'ENTER YOUR EMAIL PLEASE', SH_NAME ); ?>" autocomplete="off" />
								</div>
							</div>

						</div>
					<?php endif; ?>
					<form id="credit_card_form" class="credit-card-options">
						<div class="other-amount card">
							<div class="row">
								<div id="payment-errors"></div>
								<div class="col-md-8 col-md-offset-2">
									<input id="amount" name="amount" type="text" placeholder="<?php _e( 'ENTER YOUR AMOUNT PLEASE', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-8 col-md-offset-2">
									<input id="card_number" type="text" placeholder="<?php _e( 'Enter Your 16 digit Card Number', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-4 col-md-offset-2">
									<select class="select" id="card-mnth">
										<?php
										$mnth = range( 1, 12 );
										foreach ( $mnth as $m ) {
											if ( $m <= 9 ) {
												echo '<option value="0' . $m . '">' . $m . '</option>';
											} else {
												echo '<option value="' . $m . '">' . $m . '</option>';
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-4 col-md-offset-0">
									<select class="select" id="card-year">
										<?php
										$mnth = range( 2015, 2025 );
										foreach ( $mnth as $m ) {
											echo '<option value="' . $m . '">' . $m . '</option>';
										}
										?>
									</select>
								</div>
								<div class="col-md-8 col-md-offset-2">
									<input id="cvc" type="text" placeholder="<?php _e( 'Card Verification Number', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-12">
									<input id="submitBtn" type="submit" value="<?php _e( 'DONATE NOW', SH_NAME ); ?>" />
								</div>
							</div>
						</div>
					</form>

					<form id="checkout2_form" class="checkout2-options">
						<div class="other-amount checkout2">
							<div class="row">
								<div id="payment-errors"></div>
								<div class="col-md-8 col-md-offset-2">
									<input id="amount" name="amount" type="text" placeholder="<?php _e( 'ENTER YOUR AMOUNT PLEASE', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-8 col-md-offset-2">
									<?php
									if ( sh_country_list() ) {
										echo '<select class="select" id="checkout2_country">';
										foreach ( sh_country_list() as $k => $v ) {
											echo '<option id="' . $k . '">' . $v . '</option>';
										}
										echo '</select>';
									}
									?>
								</div>

								<div class="col-md-8 col-md-offset-2">
									<input id="chekcout2_address" type="text" placeholder="<?php _e( 'Enter Your Address', SH_NAME ); ?>" autocomplete="off" />
								</div>

								<div class="col-md-4 col-md-offset-2">
									<input id="chekcout2_city" type="text" placeholder="<?php _e( 'Enter Your City', SH_NAME ); ?>" autocomplete="off" />
								</div>

								<div class="col-md-4">
									<input id="chekcout2_state" type="text" placeholder="<?php _e( 'Enter Your State', SH_NAME ); ?>" autocomplete="off" />
								</div>

								<div class="col-md-4 col-md-offset-2">
									<input id="chekcout2_zip_code" type="text" placeholder="<?php _e( 'Enter Your Zip Code', SH_NAME ); ?>" autocomplete="off" />
								</div>

								<div class="col-md-4">
									<input id="chekcout2_contact_no" type="text" placeholder="<?php _e( 'Enter Your Phone Number', SH_NAME ); ?>" autocomplete="off" />
								</div>

								<div class="col-md-8 col-md-offset-2">
									<input id="card_number" type="text" placeholder="<?php _e( 'Enter Your 16 digit Card Number', SH_NAME ); ?>" autocomplete="off" />
								</div>

								<div class="col-md-4 col-md-offset-2">
									<select class="select" id="card-mnth">
										<?php
										$mnth = range( 1, 12 );
										foreach ( $mnth as $m ) {
											if ( $m <= 9 ) {
												echo '<option value="0' . $m . '">' . $m . '</option>';
											} else {
												echo '<option value="' . $m . '">' . $m . '</option>';
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-4 col-md-offset-0">
									<select class="select" id="card-year">
										<?php
										$mnth = range( 2015, 2025 );
										foreach ( $mnth as $m ) {
											echo '<option value="' . $m . '">' . $m . '</option>';
										}
										?>
									</select>
								</div>
								<div class="col-md-8 col-md-offset-2">
									<input id="cvc" type="text" placeholder="<?php _e( 'Card Verification Number', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-12">
									<input id="submitBtn" type="submit" value="<?php _e( 'DONATE NOW', SH_NAME ); ?>" />
								</div>
							</div>
						</div>
					</form>

					<form id="braintree_form" class="braintree-options">
						<div class="other-amount braintree">
							<div class="row">
								<div id="payment-errors"></div>
								<div class="col-md-8 col-md-offset-2">
									<input id="amount" name="amount" type="text" placeholder="<?php _e( 'ENTER YOUR AMOUNT PLEASE', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-8 col-md-offset-2">
									<input id="card_number" type="text" placeholder="<?php _e( 'Enter Your 16 digit Card Number', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-4 col-md-offset-2">
									<select class="select" id="card-mnth">
										<?php
										$mnth = range( 1, 12 );
										foreach ( $mnth as $m ) {
											if ( $m <= 9 ) {
												echo '<option value="0' . $m . '">' . $m . '</option>';
											} else {
												echo '<option value="' . $m . '">' . $m . '</option>';
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-4 col-md-offset-0">
									<select class="select" id="card-year">
										<?php
										$mnth = range( 2015, 2025 );
										foreach ( $mnth as $m ) {
											echo '<option value="' . $m . '">' . $m . '</option>';
										}
										?>
									</select>
								</div>
								<div class="col-md-8 col-md-offset-2">
									<input id="cvc" type="text" placeholder="<?php _e( 'Card Verification Number', SH_NAME ); ?>" autocomplete="off" />
								</div>
								<div class="col-md-12">
									<input id="submitBtn" type="submit" value="<?php _e( 'DONATE NOW', SH_NAME ); ?>" />
								</div>
							</div>
						</div>
					</form>

					<script>
		                jQuery(document).ready(function ($) {
		                    $(".select").select2();
		                });
					</script>
					<div class="paypal-donaiton-box">
						<div class="other-amount paypal">
							<?php echo $paypal->button( array( 'currency_code' => $sh_currency_code, 'item_name' => get_bloginfo( 'name' ), 'return' => $return_url ) ) ?>
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
	}
	exit;
}

add_action( 'wp_ajax_sh_paypal_donner_before', 'sh_paypal_donner_before' );
add_action( 'wp_ajax_nopriv_sh_paypal_donner_before', 'sh_paypal_donner_before' );

function sh_paypal_donner_before() {
	if ( isset( $_POST ) && sh_set( $_POST, 'action' ) == 'sh_paypal_donner_before' ) {
		if ( !session_id() ) {
			session_start();
		}
		$r = session_id();
		$temp = array( 'don_name' => sh_set( $_POST, 'don_name' ), 'don_email' => sh_set( $_POST, 'don_email' ) );
		if ( update_option( 'temp_donor_info' . $r, $temp ) ) {
			echo '1';
		} else {
			echo '0';
		}
		exit;
	}
}

//	The following code will add an extra column into custom post-type (Event, Causes) posts list table.

/*
  add_filter('manage_dict_causes_posts_columns', 'sh_add_post_columns');
  function sh_add_post_columns($columns) {
  $columns['sh_author'] = 'Author';
  return $columns;
  }

  //	fill the data into column of post lists table
  add_action('manage_posts_custom_column', 'sh_render_post_columns', 10, 2);
  function sh_render_post_columns($column_name, $id) {
  switch ($column_name) {
  case 'sh_author':
  echo 'sssssssss';
  break;
  }

  add_action('quick_edit_custom_box',  'sh_add_quick_edit', 10, 2);
  function sh_add_quick_edit($column_name, $post_type) {
  if ($column_name != 'sh_author') return;
  $meta = get_post_meta('12');
  printr($meta);

  //printr(get_the_author());
  ?>
  <fieldset class="inline-edit-col-left">
  <div class="inline-edit-col">
  <span class="title">Author</span>
  <?php $author_nonce = wp_create_nonce('my_author_nonce');  ?>
  <input type="hidden" name="sh_author_noncename" value="<?php echo esc_attr($author_nonce); ?>" id="sh_author_noncename" />
  <?php
  $authors = wst_get_author_list();
  $selected = '';
  ?>
  <select name='post_author_select' id='post_author_select'>
  <?php foreach ( $authors as $id => $author ) : ?>
  <option id="<?php echo esc_attr($id); ?>"><?php echo esc_html($author); ?></option>
  <?php endforeach; ?>
  </select>
  </div>
  </fieldset>
  <?php
  }
  add_action('save_post', 'sh_save_quick_edit_data');
  function sh_save_quick_edit_data($post_id) {
  //printr($_POST);
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
  return $post_id;
  if ( 'dict_causes' == $_POST['post_type'] ) {
  if ( !current_user_can( 'edit_page', $post_id ) )
  return $post_id;
  } else {
  if ( !current_user_can( 'edit_post', $post_id ) )
  return $post_id;
  }
  $nonce = $_REQUEST['sh_author_noncename'];
  if ( !wp_verify_nonce($nonce, 'my_author_nonce') ) {
  die( 'You are not authorized to edit this post' );
  } else {
  if (isset($_POST['post_author_select']) and ($post->post_type != 'revision') ) {
  $author_name = $_POST['post_author_select'];
  update_post_meta( $post_id, 'sh_post_author', $author_name);
  }
  }
  }
 */

add_action( 'wp_ajax_sh_2checkout_tocken_process', 'sh_2checkout_tocken_process' );
add_action( 'wp_ajax_nopriv_sh_2checkout_tocken_process', 'sh_2checkout_tocken_process' );

function sh_2checkout_tocken_process() {
	if ( isset( $_POST['action'] ) && $_POST['action'] == 'sh_2checkout_tocken_process' ) {
		$data = $_POST;
		$settings = get_option( SH_NAME );
		require_once(SH_ROOT . 'framework/modules/2checkout/Twocheckout.php');
		Twocheckout::format( 'json' );
		Twocheckout::privateKey( sh_set( $settings, 'checkout2_private_key' ) );
		Twocheckout::sellerId( sh_set( $settings, 'checkout2_account_number' ) );
		Twocheckout::verifySSL( false );
		if ( sh_set( $settings, 'checkout2_mode' ) == 'true' ) {
			Twocheckout::sandbox( true );
		} else {
			Twocheckout::sandbox( false );
		}
		$checkout = array(
			"sellerId" => sh_set( $settings, 'checkout2_account_number' ),
			"merchantOrderId" => uniqid(),
			"token" => sh_set( $data, 'token' ),
			"currency" => sh_set( $settings, 'currency_code' ),
			"total" => sh_set( $data, 'amoutn' ),
			"billingAddr" => array(
				"name" => sh_set( $data, 'name' ),
				"addrLine1" => sh_set( $data, 'address' ),
				"city" => sh_set( $data, 'city' ),
				"state" => sh_set( $data, 'state' ),
				"zipCode" => sh_set( $data, 'zipcode' ),
				"country" => sh_set( $data, 'country' ),
				"email" => sh_set( $data, 'mail' ),
				"phoneNumber" => sh_set( $data, 'phone_no' )
			),
			"shippingAddr" => array(
				"name" => sh_set( $data, 'name' ),
				"addrLine1" => sh_set( $data, 'address' ),
				"city" => sh_set( $data, 'city' ),
				"state" => sh_set( $data, 'state' ),
				"zipCode" => sh_set( $data, 'zipcode' ),
				"country" => sh_set( $data, 'country' ),
				"email" => sh_set( $data, 'mail' ),
				"phoneNumber" => sh_set( $data, 'phone_no' )
			)
		);
		try {
			$charge = Twocheckout_Charge::auth( $checkout );
			$result = json_decode( $charge );
			if ( sh_set( sh_set( $result, 'response' ), 'responseMsg' ) == 'Successfully authorized the provided credit card' ) {
				$new = sh_set( $data, 'amoutn' );
				$settings['paypal_raised'] = $new;
				if ( isset( $_SESSION['donation_type_popup'] ) && $_SESSION['donation_type_popup'] == 'dict_causes' ) {
					$id = $_SESSION['donation_id_popup'];
					$cause_donation = array();
					$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
					array_push(
							$cause_donation, array(
						'donner_name' => sh_set( $_POST, 'name' ),
						'donner_email' => sh_set( $_POST, 'mail' ),
						'transaction_id' => sh_set( sh_set( $result, 'response' ), 'transactionId' ),
						'transaction_type' => '2Checkout',
						'payment_type' => 'Instant',
						'order_time' => current_time( 'mysql' ),
						'amount' => sh_set( $data, 'amoutn' ),
						'currency_code' => sh_set( sh_set( $result, 'response' ), 'currencyCode' ),
						'fee_amount' => '',
						'settle_amount' => sh_set( $data, 'amoutn' ),
						'payment_status' => sh_set( sh_set( $result, 'response' ), 'responseCode' ),
						'pending_reason' => '',
						'payer_id' => sh_set( sh_set( $result, 'response' ), 'orderNumber' ),
						'ship_to_name' => '',
						'donation_type' => __( 'Single', SH_NAME ),
							)
					);
					$get_old = get_post_meta( $id, '_dict_causes_settings', true );
					$c_collect_ = (sh_set( $get_old, 'donation_collected' )) ? sh_set( $get_old, 'donation_collected' ) : 0;
					$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
					foreach ( $get_old as $k => $o ) {
						if ( $k == 'donation_collected' ) {
							$get_old['donation_collected'] = number_format( $updated );
						}
					}
					update_post_meta( $id, '_dict_causes_settings', $get_old );
					update_post_meta( $id, 'single_causes_donation', $cause_donation );
					unset( $_SESSION['donation_type_popup'] );
					unset( $_SESSION['donation_id_popup'] );
				} elseif ( isset( $_SESSION['donation_type_popup'] ) && $_SESSION['donation_type_popup'] == 'dict_project' ) {
					$id = $_SESSION['donation_id_popup'];
					$cause_donation = array();
					$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
					array_push(
							$cause_donation, array(
						'donner_name' => sh_set( $_POST, 'name' ),
						'donner_email' => sh_set( $_POST, 'mail' ),
						'transaction_id' => sh_set( sh_set( $result, 'response' ), 'transactionId' ),
						'transaction_type' => '2Checkout',
						'payment_type' => 'Instant',
						'order_time' => current_time( 'mysql' ),
						'amount' => sh_set( $data, 'amoutn' ),
						'currency_code' => sh_set( sh_set( $result, 'response' ), 'currencyCode' ),
						'fee_amount' => '',
						'settle_amount' => sh_set( $data, 'amoutn' ),
						'payment_status' => sh_set( sh_set( $result, 'response' ), 'responseCode' ),
						'pending_reason' => '',
						'payer_id' => sh_set( sh_set( $result, 'response' ), 'orderNumber' ),
						'ship_to_name' => '',
						'donation_type' => __( 'Single', SH_NAME ),
							)
					);
					$get_old = get_post_meta( $id, '_dict_project_settings', true );
					$c_collect_ = (sh_set( $get_old, 'amount_needed' )) ? sh_set( $get_old, 'amount_needed' ) : 0;
					$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
					foreach ( $get_old as $k => $o ) {
						if ( $k == 'amount_needed' ) {
							$get_old['amount_needed'] = number_format( $updated );
						}
					}
					update_post_meta( $id, '_dict_project_settings', $get_old );
					update_post_meta( $id, 'single_causes_donation', $cause_donation );
					unset( $_SESSION['donation_type_popup'] );
					unset( $_SESSION['donation_id_popup'] );
				} else {
					$cause_donation = array();
					$cause_donation = (get_option( 'general_donation' )) ? get_option( 'general_donation' ) : array();
					array_push(
							$cause_donation, array(
						'donner_name' => sh_set( $_POST, 'name' ),
						'donner_email' => sh_set( $_POST, 'mail' ),
						'transaction_id' => sh_set( sh_set( $result, 'response' ), 'transactionId' ),
						'transaction_type' => '2Checkout',
						'payment_type' => 'Instant',
						'order_time' => current_time( 'mysql' ),
						'amount' => sh_set( $data, 'amoutn' ),
						'currency_code' => sh_set( sh_set( $result, 'response' ), 'currencyCode' ),
						'fee_amount' => '',
						'settle_amount' => sh_set( $data, 'amoutn' ),
						'payment_status' => sh_set( sh_set( $result, 'response' ), 'responseCode' ),
						'pending_reason' => '',
						'payer_id' => sh_set( sh_set( $result, 'response' ), 'orderNumber' ),
						'ship_to_name' => '',
						'donation_type' => __( 'Single', SH_NAME ),
							)
					);

					$donation_data = get_option( SH_NAME );
					$c_collect_ = (sh_set( $donation_data, 'paypal_raised' )) ? sh_set( $donation_data, 'paypal_raised' ) : 0;
					$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) sh_set( $data, 'amoutn' );
					foreach ( $donation_data as $k => $o ) {
						if ( $k == 'paypal_raised' ) {
							$donation_data['paypal_raised'] = number_format( $updated );
						}
					}
					update_option( SH_NAME, $donation_data );
					update_option( 'general_donation', $cause_donation );
				}
				echo __( 'Payment Made Successfully!', SH_NAME );
			} else {
				echo __( 'Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction. You can try again or use another card.', SH_NAME );
			}
		} catch ( Twocheckout_Error $e ) {
			echo $e->getMessage();
		}
	}
	exit;
}

add_action( 'wp_ajax_sh_braintree_tocken_process', 'sh_braintree_tocken_process' );
add_action( 'wp_ajax_nopriv_sh_braintree_tocken_process', 'sh_braintree_tocken_process' );

function sh_braintree_tocken_process() {
	if ( isset( $_POST['action'] ) && $_POST['action'] == 'sh_braintree_tocken_process' ) {
		$data = $_POST;
		$settings = get_option( SH_NAME );
		require(SH_ROOT . 'framework/modules/braintree/Braintree.php');
		Braintree\Configuration::environment( sh_set( $settings, 'braintree_mode' ) );
		Braintree\Configuration::merchantId( sh_set( $settings, 'braintree_merchant_id' ) );
		Braintree\Configuration::publicKey( sh_set( $settings, 'braintree_publish_key' ) );
		Braintree\Configuration::privateKey( sh_set( $settings, 'braintree_private_key' ) );
		$result = Braintree\Transaction::sale( array(
					'amount' => sh_set( $data, 'amount' ),
					'creditCard' => array(
						'cardholderName' => sh_set( $data, 'name' ),
						'number' => sh_set( $data, 'card_num' ),
						'expirationDate' => sh_set( $data, 'mnth' ) . '/' . sh_set( $data, 'exp_year' ),
						'cvv' => sh_set( $data, 'cvc' ),
					),
					'options' => array( 'submitForSettlement' => true )
				) );
		if ( $result->success ) {
			$new = sh_set( $data, 'amount' );
			$settings['paypal_raised'] = $new;
			if ( isset( $_SESSION['donation_type_popup'] ) && $_SESSION['donation_type_popup'] == 'dict_causes' ) {
				$id = $_SESSION['donation_id_popup'];
				$cause_donation = array();
				$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
				array_push(
						$cause_donation, array(
					'donner_name' => sh_set( $_POST, 'name' ),
					'donner_email' => sh_set( $_POST, 'email' ),
					'transaction_id' => $result->transaction->id,
					'transaction_type' => 'Braintree',
					'payment_type' => 'Instant',
					'order_time' => current_time( 'mysql' ),
					'amount' => $result->transaction->amount,
					'currency_code' => $result->transaction->currencyIsoCode,
					'fee_amount' => '',
					'settle_amount' => $result->transaction->amount,
					'payment_status' => $result->transaction->statusHistory[0]->status,
					'pending_reason' => '',
					'payer_id' => $result->transaction->id,
					'ship_to_name' => '',
					'donation_type' => __( 'Single', SH_NAME ),
						)
				);
				$get_old = get_post_meta( $id, '_dict_causes_settings', true );
				$c_collect_ = (sh_set( $get_old, 'donation_collected' )) ? sh_set( $get_old, 'donation_collected' ) : 0;
				$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
				foreach ( $get_old as $k => $o ) {
					if ( $k == 'donation_collected' ) {
						$get_old['donation_collected'] = number_format( $updated );
					}
				}
				update_post_meta( $id, '_dict_causes_settings', $get_old );
				update_post_meta( $id, 'single_causes_donation', $cause_donation );
				unset( $_SESSION['donation_type_popup'] );
				unset( $_SESSION['donation_id_popup'] );
			} elseif ( isset( $_SESSION['donation_type_popup'] ) && $_SESSION['donation_type_popup'] == 'dict_project' ) {
				$id = $_SESSION['donation_id_popup'];
				$cause_donation = array();
				$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
				array_push(
						$cause_donation, array(
					'donner_name' => sh_set( $_POST, 'name' ),
					'donner_email' => sh_set( $_POST, 'email' ),
					'transaction_id' => $result->transaction->id,
					'transaction_type' => 'Braintree',
					'payment_type' => 'Instant',
					'order_time' => current_time( 'mysql' ),
					'amount' => $result->transaction->amount,
					'currency_code' => $result->transaction->currencyIsoCode,
					'fee_amount' => '',
					'settle_amount' => $result->transaction->amount,
					'payment_status' => $result->transaction->statusHistory[0]->status,
					'pending_reason' => '',
					'payer_id' => $result->transaction->id,
					'ship_to_name' => '',
					'donation_type' => __( 'Single', SH_NAME ),
						)
				);
				$get_old = get_post_meta( $id, '_dict_project_settings', true );
				$c_collect_ = (sh_set( $get_old, 'amount_needed' )) ? sh_set( $get_old, 'amount_needed' ) : 0;
				$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
				foreach ( $get_old as $k => $o ) {
					if ( $k == 'amount_needed' ) {
						$get_old['amount_needed'] = number_format( $updated );
					}
				}
				update_post_meta( $id, '_dict_project_settings', $get_old );
				update_post_meta( $id, 'single_causes_donation', $cause_donation );
				unset( $_SESSION['donation_type_popup'] );
				unset( $_SESSION['donation_id_popup'] );
			} else {
				$cause_donation = array();
				$cause_donation = (get_option( 'general_donation' )) ? get_option( 'general_donation' ) : array();
				array_push(
						$cause_donation, array(
					'donner_name' => sh_set( $_POST, 'name' ),
					'donner_email' => sh_set( $_POST, 'email' ),
					'transaction_id' => $result->transaction->id,
					'transaction_type' => 'Braintree',
					'payment_type' => 'Instant',
					'order_time' => current_time( 'mysql' ),
					'amount' => $result->transaction->amount,
					'currency_code' => $result->transaction->currencyIsoCode,
					'fee_amount' => '',
					'settle_amount' => $result->transaction->amount,
					'payment_status' => $result->transaction->statusHistory[0]->status,
					'pending_reason' => '',
					'payer_id' => $result->transaction->id,
					'ship_to_name' => '',
					'donation_type' => __( 'Single', SH_NAME ),
						)
				);

				$donation_data = get_option( SH_NAME );
				$c_collect_ = (sh_set( $donation_data, 'paypal_raised' )) ? sh_set( $donation_data, 'paypal_raised' ) : 0;
				$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) sh_set( $data, 'amount' );
				foreach ( $donation_data as $k => $o ) {
					if ( $k == 'paypal_raised' ) {
						$donation_data['paypal_raised'] = number_format( $updated );
					}
				}
				update_option( SH_NAME, $donation_data );
				update_option( 'general_donation', $cause_donation );
			}
			echo __( 'Payment Made Successfully!', SH_NAME );
		} else if ( $result->transaction ) {
			echo _e( "Error processing transaction: code " . $result->transaction->processorResponseCode . " and text " . $result->transaction->processorResponseText . "", 'wp-appointment' );
		} else {
			echo _e( "Validation errors:", 'wp-appointment' ) . $result->errors->deepAll();
		}
	}
	exit;
}
