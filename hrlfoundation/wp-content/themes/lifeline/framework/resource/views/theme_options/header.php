<div id="headbar">
    <div class="container">
        <div id="panel-logo">
            <img alt="" src="<?php echo SH_FRW_URL; ?>resource/images/logo.png">
        </div>
        <div class="import_area">
            <div class="left_side">
                <a title="" id="install_button" href="javascript:void(0)" class="help"><?php _e( 'Import Demo Data', SH_NAME ); ?></a>
            </div>
            <div class="left_side">
            	<select id="demos">
                    <option value="lifeline_1"><?php esc_html_e('Home Simple 1', SH_NAME);?></option>
                    <option value="lifeline_2"><?php esc_html_e('Home Parallax Style', SH_NAME);?></option>
                    <option value="lifeline_3"><?php esc_html_e('Home Non-Profit', SH_NAME);?></option>
                    <option value="lifeline_4"><?php esc_html_e('Home Charity ORG', SH_NAME);?></option>
                    <option value="lifeline_5"><?php esc_html_e('Home Traditional Style', SH_NAME);?></option>
                    <option value="lifeline_6"><?php esc_html_e('Home Masonary Style', SH_NAME);?></option>
                    <option value="lifeline_7"><?php esc_html_e('Home With Video', SH_NAME);?></option>
                    <option value="lifeline_8"><?php esc_html_e('Home Organization', SH_NAME);?></option>
                    <option value="lifeline_9"><?php esc_html_e('Home Organization 2', SH_NAME);?></option>
                    <option value="lifeline_10"><?php esc_html_e('Home With Layer Slider', SH_NAME);?></option>
                    <option value="lifeline_11"><?php esc_html_e('Home Minimalist 1', SH_NAME);?></option>
                    <option value="lifeline_12"><?php esc_html_e('Home Minimalist 2', SH_NAME);?></option>
                    <option value="lifeline_13"><?php esc_html_e('Home Organization 2016', SH_NAME);?></option>
                </select>
            </div>
        </div>

        <div class="sitelink">
            <a title="" href="#"><?php _e( 'VERSION 4.4.3', SH_NAME ) ?></a>
            <a title="" href="http://themeforest.net/user/webinane/portfolio"><?php _e( 'Visit Our Themes', SH_NAME ); ?></a>
        </div>
    </div>
</div>
<div class="overlay"></div>
<div class="importer_result">
    <div class="importer_heading">
        <span>X</span>
        <h1><?php _e( 'Import Results', SH_NAME ) ?></h1>
    </div>
    <div class="result"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#install_button').on('click', function () {
            var check = confirm("<?php _e( "Are you sure installing demo data?  Please be aware that the demo data comprises a significant amount of content, and we suggest this demo data be installed in a local host ( ie home or work computer using wamp or mamp ) not in the online site.", SH_NAME ) ?>");
            if (check == false) {
                return false;
            }
            if (jQuery(this).hasClass('is_disabled')) {
                return false;
            }
            jQuery('#install_button').addClass('is_disabled');
            var loading = $('<span class="wobblebar">Loading&#8230;</span>').insertAfter('#install_button');
            var data = 'data=' + $('select#demos').val() + '&action=theme-install-demo-data';
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                success: function (response) {
                    jQuery('.importer_result .result').html('').hide();
                    var height = jQuery('html').height();
                    jQuery('.overlay').css({
                        'background': 'rgba(0,0,0,0.65)',
                        'position': 'fixed',
                        'top': '0',
                        'left': '0',
                        'width': '100%',
                        'height': '100%',
                        'z-index': '9999999'
                    });
                    jQuery('.overlay').fadeIn(500, 'swing');
                    jQuery('.importer_result').fadeIn(500, 'swing');
                    jQuery('.importer_result .result').append(response);
                    jQuery('.importer_result .result').fadeIn(500, 'swing');
                    loading.remove();
                    var done = jQuery('<span class="theme-install-done">Done!</span>').insertAfter('.left_side');
                    setTimeout(function () {
                        jQuery(done).fadeOut(500, 'swing');
                    }, 5000);
                },
            });
            return false;
        });

        jQuery('.importer_result span').click(function () {
            jQuery('.result').fadeOut(500, 'swing');
            jQuery('.importer_result').fadeOut(500, 'swing');
            jQuery('.overlay').fadeOut(500, 'swing');
            jQuery('#install_button').removeClass('is_disabled');
        });
    });

</script>
