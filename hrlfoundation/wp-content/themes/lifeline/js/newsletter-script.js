jQuery(document).ready(function ($) {

    $('#newsletter-form-submit').on('click', function () {

        var thiss = this;

        var email = jQuery("#email").val();

        var data = 'email=' + email + '&action=lifeline_newsletter_module';

        var button_id = '#' + $(this).attr('id');

        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            data: data,
            beforeSend: function () {

                jQuery('div.loading').fadeIn(500);

            },
            success: function (response) {

                jQuery('div.loading').fadeOut(500);

                $(button_id).removeAttr('disabled');

                $('form#newsletter-email .newsletter-message').html(response);
               
            }

        });

        return false;

    });

});