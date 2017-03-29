;
(function ($) {
    var data = 'action=sh_update_theme';
    $.fn.dfupdatebutton = function () {
        this.on('click', function () {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                beforeSend: function () {
                    jQuery('div.overlay').css({
                        'background': 'rgba(0,0,0,0.65)',
                        'position': 'fixed',
                        'top': '0',
                        'left': '0',
                        'width': '100%',
                        'height': '100%',
                        'z-index': '9999999'
                    });
                    jQuery('.overlay').fadeIn(500, 'swing');
                },
                success: function (res) {
                    alert(res);
                    jQuery('.overlay').fadeOut(500, 'swing');
                }
            });
            return false;
        });
    };
}(jQuery));
jQuery(document).ready(function ($) {
    $('.button-update, a#update_notifier_inner, div.update_btn > a#update_notifier_inner').dfupdatebutton();
});