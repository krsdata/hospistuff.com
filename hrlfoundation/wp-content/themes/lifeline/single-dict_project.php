<?php
sh_custom_header();
$Settings = get_option(SH_NAME);
$PostSettings = get_post_meta(get_the_ID(), '_' . sh_set($post, 'post_type') . '_settings', true);
$attachments = get_posts(array('post_type' => 'attachment', 'post_parent' => get_the_ID(), 'showposts' => -1));
$sidebar = sh_set($PostSettings, 'sidebar') ? sh_set($PostSettings, 'sidebar') : '';
$column = ($sidebar) ? 'nine-column' : 'twelve-column';
$pos = (sh_set($PostSettings, 'sidebar_pos')) ? sh_set($PostSettings, 'sidebar_pos') : 'right';
$paypal = $GLOBALS['_sh_base']->donation;
$percent = (sh_set($PostSettings, 'amount_needed')) ? (int) str_replace(',', '', sh_set($PostSettings, 'spent_amount')) / (int) str_replace(',', '', sh_set($PostSettings, 'amount_needed')) : 0;
$donation_percentage = round($percent * 100, 2);

$symbol = (sh_set($PostSettings, 'spent_amount_currency')) ? sh_set($PostSettings, 'spent_amount_currency') : '$';
$sh_currency_code = (sh_set($PostSettings, 'currency_code')) ? sh_set($PostSettings, 'currency_code') : 'USD';
$_SESSION['sh_causes_id'] = get_the_ID();
$_SESSION['sh_causes_url'] = get_permalink();
$_SESSION['sh_causes_page'] = true;
$_SESSION['sh_currency_code'] = $sh_currency_code;
$_SESSION['sh_donation_needed'] = sh_set($PostSettings, 'amount_needed');
$_SESSION['sh_donation_collected'] = sh_set($PostSettings, 'spent_amount');
$_SESSION['sh_currency_symbol'] = $symbol;
$_SESSION['sh_post_type'] = 'project';
$paypal_res = '';
if (isset($_GET['recurring_pp_return']) && $_GET['recurring_pp_return'] == 'return') {
    $paypal_res = require_once(get_template_directory() . '/framework/modules/pp_recurring/review.php');
}
if ($notif = $paypal->_paypal->handleNotification())
    $paypal_res = $paypal->single_pament_result($notif);
?>
<div class="top-image"><img src="<?php echo sh_set($PostSettings, 'top_image'); ?>" alt="" /></div>
<!-- Page Top Image -->
<section class="inner-page<?php echo ( sh_set($PostSettings, 'sidebar_pos') == 'left' ) ? ' switch' : ''; ?>">
    <div class="container">
        <div class="left-content <?php echo $column ?>">
            <div  id="post-<?php the_ID(); ?>" <?php post_class("post"); ?>>
                <?php while (have_posts()): the_post(); ?>
                    <?php the_post_thumbnail('1170x455'); ?>
                    <span class="category"><?php _e('In ', SH_NAME); ?> <?php echo get_the_term_list(get_the_ID(), 'project_category', '', ',', '') ?> </span><!-- Categories -->
                    <h1><?php the_title(); ?></h1>

                    <?php if(sh_set($PostSettings, 'show_proj_donation_bar') == "true") : ?>
                    <ul class="post-meta">
                        <li><a href="" title=""><i class="icon-calendar-empty"></i><span><?php echo get_the_date('m-d-y', get_the_id()); ?></a></li>
                        <?php
                        $Author = get_the_author();
                        if (!empty($Author)) :
                            ?>

                            <li><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" title=""><i class="icon-user"></i><?php echo _e('By', SH_NAME); ?> <?php echo get_the_author(); ?></a></li>
                        <?php endif; ?>
                        <?php if (sh_set($PostSettings, 'location')) : ?>

                            <li><a href="" title=""><i class="icon-map-marker"></i><?php echo __('In', SH_NAME) . ' ' . sh_set($PostSettings, 'location'); ?></a></li>
                        <?php endif; ?>
                        <li>
                            <p><span><?php echo sh_set($PostSettings, 'amount_needed_currency'); ?></span> <?php echo sh_set($PostSettings, 'amount_needed'); ?></p>
                            <?php if (sh_set($Settings, 'donate_method') == 'true') : ?>		   
                                <span data-toggle="modal" data-url="<?php echo get_permalink() ?>" data-type="project" data-id="<?php echo get_the_ID() ?>" data-target="#myModal"  class="btn-don donate-btn"><?php _e('Donate Us', SH_NAME) ?></span>
                            <?php else: ?>
                                <span><?php echo $paypal->button(array('currency_code' => $sh_currency_code, 'item_name' => get_bloginfo('name'), 'return' => get_permalink())); ?></span>

                            <?php endif; ?>
                        </li>
                    </ul>
                    <?php endif; ?>
                    
                    
                    <div class="post-desc">
                        <p><?php the_content(); ?></p></div>
                    <div class="cloud-tags">
                        <?php the_tags('<h3 class="sub-head">' . __('Tags Clouds', SH_NAME) . '</h3>', ''); ?>
                    </div><!-- Tags -->	

                    <?php if (sh_set($Settings, 'page_comments_status') == 'true'): ?> 
                        <div class="comments"><?php comments_template(); ?></div>
                    <?php endif; ?>

                <?php endwhile; ?>
            </div>
        </div>
        <div class="sidebar three-column pull-<?php echo $pos ?>">
            <?php dynamic_sidebar($sidebar); ?>
        </div>
    </div>
</section> 
<?php get_footer(); ?>
