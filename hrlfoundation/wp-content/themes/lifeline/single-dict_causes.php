<?php
sh_custom_header();
$Settings = get_option(SH_NAME);
$PostSettings = get_post_meta(get_the_ID(), '_' . sh_set($post, 'post_type') . '_settings', true);
$attachments = explode(',', sh_set($PostSettings, 'gallery'));
$sidebar = sh_set($PostSettings, 'sidebar') ? sh_set($PostSettings, 'sidebar') : '';
$col_class = sh_set($PostSettings, 'sidebar') ? 'col-md-9' : 'col-md-12';
$paypal = $GLOBALS['_sh_base']->donation;
$percent = (sh_set($PostSettings, 'donation_needed')) ? (int) str_replace(',', '', sh_set($PostSettings, 'donation_collected')) / (int) str_replace(',', '', sh_set($PostSettings, 'donation_needed')) : 0;
$donation_percentage = round($percent * 100, 2);
$symbol = (sh_set($PostSettings, 'currency_symbol')) ? sh_set($PostSettings, 'currency_symbol') : '$';
$sh_currency_code = (sh_set($PostSettings, 'currency_code')) ? sh_set($PostSettings, 'currency_code') : 'USD';
$_SESSION['sh_causes_id'] = get_the_ID();
$_SESSION['sh_causes_url'] = get_permalink();
$_SESSION['sh_causes_page'] = true;
$_SESSION['sh_currency_code'] = $sh_currency_code;
$_SESSION['sh_donation_needed'] = sh_set($PostSettings, 'donation_needed');
$_SESSION['sh_donation_collected'] = sh_set($PostSettings, 'donation_collected');
$_SESSION['sh_currency_symbol'] = $symbol;
$_SESSION['sh_post_type'] = 'causes';
$paypal_res = '';

if (isset($_GET['recurring_pp_return']) && $_GET['recurring_pp_return'] == 'return') {
    $paypal_res = require_once(get_template_directory() . '/framework/modules/pp_recurring/review.php');
}
if ($notif = $paypal->_paypal->handleNotification())
    $paypal_res = $paypal->single_pament_result($notif);
?>
<div class="top-image"><img src="<?php echo sh_set($PostSettings, 'top_image'); ?>" alt="" /></div>
<!-- Page Top Image -->
<section class="inner-page <?php echo ( sh_set($PostSettings, 'sidebar_pos') == 'left' ) ? ' switch' : ''; ?>">
    <div class="container">
        <div class="row">
            <div class="left-content <?php echo $col_class; ?>">
                <?php if (have_posts()): while (have_posts()): the_post(); ?>
                        <div class="post">
                            <div class="causes-single">
                                <div class="tab-content" id="myTabContent">
                                    <?php
                                    $count = 1;
                                    foreach ($attachments as $att):
                                        if ($att):
                                            $active = ($count == 1) ? 'active' : '';
                                            ?>
                                            <div id="cause<?php echo $att; ?>" class="tab-pane fade in <?php echo $active; ?> ">
                                                <img src="<?php echo sh_set(wp_get_attachment_image_src($att, '1170x455'), 0); ?>" alt="" />
                                            </div>
                                            <?php
                                            $count++;
                                            if ($count > 3)
                                                break;
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                                <ul class="nav nav-tabs" id="myTab">
                                    <?php
                                    $count = 1;
                                    foreach ($attachments as $att):
                                        if ($att):
                                            $active = ($count == 1) ? 'active' : '';
                                            ?>
                                            <li class="<?php echo $active; ?>">
                                                <a data-toggle="tab" href="#cause<?php echo $att; ?>">
                                                    <img src="<?php echo sh_set(wp_get_attachment_image_src($att, '150x150'), 0); ?>" alt="" /></a>
                                            </li>
                                            <?php
                                            $count++;
                                            if ($count > 3)
                                                break;
                                        endif;
                                    endforeach;
                                    ?>

                                </ul>					
                            </div>
                            
                            <?php if( sh_set($PostSettings, 'show_donation_bar') == "true" ) : ?>
                            <div class="cause-bar">
                                <div class="cause-box"><h3><span><?php echo $symbol; ?></span><?php echo sh_set($PostSettings, 'donation_needed'); ?></h3><i><?php _e('NEEDED DONATION', SH_NAME); ?></i></div>
                                <div class="cause-progress">
                                    <div class="progress-report">
                                        <h6><?php _e('PHASES', SH_NAME) ?></h6>
                                        <span><?php echo $donation_percentage ?>%</span>
                                        <div class="progress pattern">
                                            <div class="progress-bar" style="width: <?php echo $donation_percentage ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cause-box"><h3><span><?php echo $symbol; ?></span><?php echo sh_set($PostSettings, 'donation_collected'); ?></h3><i><?php _e('COLLECTED DONATION', SH_NAME) ?></i></div>
                                <div class="cause-box donate-drop-btn" data-url="<?php echo get_permalink() ?>" data-type="post" data-id="<?php echo get_the_ID() ?>"><h4><?php _e('DONATE NOW', SH_NAME); ?></h4></div>
                            </div>
                            <?php endif; ?>
                                
                            <span class="category"><?php _e("In ", SH_NAME); ?><?php echo get_the_term_list(get_the_ID(), 'causes_category', '', ',', '') ?></span><!-- Categories -->
                            <h1><?php the_title(); ?></h1>

                            <ul class="    post-meta">

                                <li><a href="" title=""><i class="icon-calendar-empty"></i><span><?php echo sh_set($PostSettings, 'start_date'); ?></span></a></li>
                                <li><a href="" title=""><i></i><?php _e("To", SH_NAME); ?> 	<span><?php echo sh_set($PostSettings, 'end_date'); ?></span></a></li>
                                <li><a href="" title=""><i class="icon-user"></i><?php _e("By ", SH_NAME); ?> <?php the_author(); ?></a></li>
                                <li><a href="" title=""><i class="icon-map-marker"></i><?php _e("In", SH_NAME); ?> <?php echo sh_set($PostSettings, 'location'); ?></a></li>
                                <li>
                                    <p><span><?php echo $symbol; ?></span> <?php echo sh_set($PostSettings, 'donation_needed'); ?></p>


                                    <span><?php _e('Needed Donation', SH_NAME); ?></span>

                                </li>

                            </ul>

                            <div class= "post-desc">

                                <?php the_content(); ?>						
                            </div>

                            <div class="cloud-tags">
                                <?php the_tags('<h3 class="sub-head">' . __('Tags Clouds', SH_NAME) . '</h3>', ''); ?>
                            </div><!-- Tags -->	
                            <?php
                            if (is_single() && comments_open())
                                comments_template();
                            ?>

                        </div>
                        <?php
                    endwhile;
                endif;
                ?>	
            </div>
            <?php if ($sidebar) : ?>
                <div class="sidebar col-md-3 pull-right">
                    <?php dynamic_sidebar($sidebar); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</section> 


<?php get_footer(); ?>
