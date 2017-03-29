/*** Document Ready Functions ***/
jQuery(document).ready(function ($) {
	jQuery("body").removeClass('reply');
	
    $(".credit-card").live("click", function () {
        $(this).addClass("active");
        $('a.checkout2').removeClass("active");
        $('a.braintree').removeClass("active");
        $('a.paypal-donation').removeClass("active");
        $(this).parent().parent().find("form.credit-card-options").slideDown();
        $('a.checkout2').parent().parent().find("form.checkout2-options").slideUp();
        $('a.braintree').parent().parent().find("form.braintree-options").slideUp();
        $('a.paypal-donation').parent().parent().find(".paypal-donaiton-box").slideUp();
        return false;
    });
    $(".checkout2").live("click", function () {
        $(this).addClass("active");
        $('a.credit-card').removeClass("active");
        $('a.braintree').removeClass("active");
        $('a.paypal-donation').removeClass("active");
        $(this).parent().parent().find("form.checkout2-options").slideDown();
        $('a.credit-card').parent().parent().find("form.credit-card-options").slideUp();
        $('a.braintree').parent().parent().find("form.braintree-options").slideUp();
        $('a.paypal-donation').parent().parent().find(".paypal-donaiton-box").slideUp();
        return false;
    });
    $(".braintree").live("click", function () {
        $(this).addClass("active");
        $('a.credit-card').removeClass("active");
        $('a.checkout2').removeClass("active");
        $('a.paypal-donation').removeClass("active");
        $(this).parent().parent().find("form.braintree-options").slideDown();
        $('a.credit-card').parent().parent().find("form.credit-card-options").slideUp();
        $('a.checkout2').parent().parent().find("form.checkout2-options").slideUp();
        $('a.paypal-donation').parent().parent().find(".paypal-donaiton-box").slideUp();
        return false;
    });
    $(".paypal-donation").live("click", function () {
        $(this).addClass("active");
        $('a.checkout2').removeClass("active");
        $('a.braintree').removeClass("active");
        $('a.credit-card').removeClass("active");
        $('a.credit-card').parent().parent().find("form.credit-card-options").slideUp();
        $('a.braintree').parent().parent().find("form.braintree-options").slideUp();
        $('a.checkout2').parent().parent().find("form.checkout2-options").slideUp();
        $(this).parent().parent().find(".paypal-donaiton-box").slideDown();
        return false;
    });
    $(function () {
        $('input[name="amount"], #card_number, #cvc').live('keydown', function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) {
                e.preventDefault();
            } else {
                var key = e.keyCode;
                if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                    e.preventDefault();
                }
            }
        });
    });
    $('div.amount-btns a').live('click', function () {
        var amount = $(this).children('span').html();
        $('div.other-amount > form input[name="amount"], div.other-amount.card input[name="amount"]').attr('value', amount);
    });
    
    
    // Responsive Header //
$(".menu-btn").on("click", function () {
    $(".responsive-menu").addClass("slidein");
    return false;
});
$(".close-btn").on("click", function () {
    $(".responsive-menu").removeClass("slidein");
    return false;
});
$(".responsive-menu li.menu-item-has-children > a").on("click", function () {
    $(this).parent().siblings().children("ul").slideUp();
    $(this).parent().siblings().removeClass("active");
    $(this).parent().children("ul").slideToggle();
    $(this).parent().toggleClass("active");
    return false;
});

// Scroll Bar //
$('.responsive-menu').perfectScrollbar();

// Responsive info Toggle //
$(".responsive-topbar-info > ul > li:first-child").addClass("active");
$(".responsive-topbar-info > ul > li").on("click", function() {
    $(this).parent().find("li").removeClass("active");
    $(this).addClass("active");
});
    
    // responsive header
//    $(".responsive-header > span").click(function () {
//        $(this).next('ul').slideToggle();
//        $(".responsive-header > ul > li > ul").slideUp();
//        $(".responsive-header > ul > li > ul > li > ul").slideUp();
//        $(".responsive-header > ul > li").removeClass('opened');
//        $(".responsive-header > ul > li > ul > li").removeClass('opened');
//    });
//    $('.responsive-header ul li a').next('ul').parent().addClass('no-link')
//    $('.no-link > a').click(function () {
//        return false;
//    });
//    $(".responsive-header > ul > li > a").click(function () {
//        $(".responsive-header > ul > li > ul").slideUp();
//        $(".responsive-header > ul > li").removeClass('opened');
//        $(this).next('ul').slideDown();
//        $(this).next('ul').parent().toggleClass('opened');
//    });
//    $(".responsive-header > ul > li > ul > li a").click(function () {
//        $(".responsive-header > ul > li > ul > li > ul").slideUp();
//        $(".responsive-header > ul > li > ul > li").removeClass('opened');
//        $(this).next('ul').slideDown();
//        $(this).next('ul').parent().toggleClass('opened');
//    });
    // layer slider
    var layer = jQuery('.wpb_layerslider_element').parent().attr('class');
    if (layer == 'col-md-12 column') {
        jQuery('.wpb_layerslider_element').parent().parent().parent().addClass('expand');
    }
    "use strict";
    $('.parallax-video').parent().parent().parent().addClass('expand');
    $('.full-section').parent().parent().parent().addClass('expand');
    var video = jQuery('#para_vid').parent().attr('class');
    if (video == 'col-md-12') {
        jQuery('#para_vid').parent().attr('class', '');
        jQuery('#para_vid').parent().parent().attr('class', '');
        jQuery('#para_vid').parent().parent().parent().attr('class', '');
    }
    $('.count').counterUp({
        delay: 10,
        time: 1000
    });
    /*full screen video*/
    var $allVideos = jQuery("iframe[src^='http://player.vimeo.com'], iframe[src^='http://www.youtube.com'], object, embed"),
            jQueryfluidEl = jQuery("#para_vid");
    $allVideos.each(function () {
        jQuery(this)
                // jQuery .data does not work on object/embed elements
                .attr('data-aspectRatio', this.height / this.width)
                .removeAttr('height')
                .removeAttr('width');
    });
    jQuery(window).resize(function () {
        var newWidth = jQueryfluidEl.width();
        $allVideos.each(function () {
            var jQueryel = jQuery(this);
            jQueryel
                    .width(newWidth)
                    .height(newWidth * jQueryel.attr('data-aspectRatio'));
        });
    }).resize();
    $('#lifeline_contactform_2 #submit').on('click', function (e) {
        e.preventDefault();
        var thisform = 'form#lifeline_contactform_2';
        var fields = $('form#lifeline_contactform_2').serialize();
        var url = $('form#lifeline_contactform_2').attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: fields,
            success: function (res) {
                $('.msgs', thisform).html(res);
            }
        });
    });
    $('#lifeline_contact_form1', '#lifeline_contactform_2').live('submit', function (e) {
        e.preventDefault();
        var thisform = this;
        var fields = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: fields,
            success: function (res) {
                $('.msgs', thisform).html(res);
            }
        });
    });
    var allservice = $('.cause-tabber li');
    $('.cause-tabber li').click(function () {
        $(allservice).removeClass("active");
    });
    /*** MESSAGE BOX TOGGLE FUNCTION ***/
    $(".message-box-title").click(function () {
        $(".message-box-title").toggleClass("opened");
        $(".message-box-title > i").toggleClass("icon-angle-down");
        $(".message-form").slideToggle();
    });
    $(".product > a").click(function () {
        $(this).parent().parent().slideUp();
    });
    /*** Responsive Menu Function ***/
    $('.ipadMenu').change(function () {
        var loc = $('option:selected', this).val();
        document.location.href = loc;
    });
    /*** ACCORDIONS ***/
    $('.accordion_content').not('.open').hide();
    $('.accordion_toggle a').click(function (e) {
        if ($(this).parent().hasClass('current')) {
            $(this).parent()
                    .removeClass('current')
                    .next('.accordion_content').slideUp();
        } else {
            $(document).find('.current')
                    .removeClass('current')
                    .next('.accordion_content').slideUp();
            $(this).parent()
                    .addClass('current')
                    .next('.accordion_content').slideDown();
        }
        e.preventDefault();
    });
    /*** ACCORDIONS ***/
    $('.accordion_content').not('.open').hide();
    $('.accordion_toggle input').click(function (e) {
        if ($(this).parent().hasClass('current')) {
            $(this).parent()
                    .removeClass('current')
                    .next('.accordion_content').slideUp();
        } else {
            $(document).find('.current')
                    .removeClass('current')
                    .next('.accordion_content').slideUp();
            $(this).parent()
                    .addClass('current')
                    .next('.accordion_content').slideDown();
        }
        e.preventDefault();
    });
    /*** STICKY MENU ***/
    var nav = $('.sticky');
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            nav.addClass("stick");
        } else {
            nav.removeClass("stick");
        }
    });
    /*** TOGGLE HEADER ***/
    $(".show-header").click(function () {
        $(".toggle-header").slideToggle();
        $(".top-bar-toggle").slideToggle();
        $(this).toggleClass("move-down");
    });
    /*** CHECKOUT PAGE FORM TOGGLE ICON ***/
    $(".form-toggle.accordion_toggle a").click(function () {
        $(this).toggleClass("pointed");
    });
    /*** Side Panel Functions ***/
    $(".panel-icon").click(function () {
        $(".side-panel").toggleClass("show");
    });
    $(".boxed-style").click(function () {
        $(".theme-layout").addClass("boxed");
        $("body").addClass('bg-body1');
    });
    $(".full-style").click(function () {
        $(".theme-layout").removeClass("boxed");
        $("body").removeClass('bg-body1');
        $("body").removeClass('bg-body2');
        $("body").removeClass('bg-body3');
        $("body").removeClass('bg-body4');
    });
    $(".pat1").click(function () {
        $("body").addClass('bg-body1');
        $("body").removeClass('bg-body2');
        $("body").removeClass('bg-body3');
        $("body").removeClass('bg-body4');
    });
    $(".pat2").click(function () {
        $("body").removeClass('bg-body1');
        $("body").addClass('bg-body2');
        $("body").removeClass('bg-body3');
        $("body").removeClass('bg-body4');
    });
    $(".pat3").click(function () {
        $("body").removeClass('bg-body1');
        $("body").removeClass('bg-body2');
        $("body").addClass('bg-body3');
        $("body").removeClass('bg-body4');
    });
    $(".pat4").click(function () {
        $("body").removeClass('bg-body1');
        $("body").removeClass('bg-body2');
        $("body").removeClass('bg-body3');
        $("body").addClass('bg-body4');
    });
    $('.countries').flexslider({
        animation: "slide",
        animationLoop: false,
        slideShow: false,
        controlNav: false,
        pausePlay: false,
        mousewheel: false,
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });
    if ($('.stories-carousel').length > 0)
    {
        $('.stories-carousel').flexslider({
            animation: "slide",
            animationLoop: true,
            controlNav: false,
            maxItems: 1,
            pausePlay: false,
            mousewheel: false,
            start: function (slider) {
                $('body').removeClass('loading');
            }
        });
    }
    var revapi;
    if (jQuery('.tp-banner-causes').length) {
        revapi = jQuery('.tp-banner-causes').revolution(
                {
                    delay: 15000,
                    startwidth: 270,
                    startheight: 184,
                    autoHeight: "on",
                    navigationType: "none",
                    navigation: {
                        arrows:{enable:true}				
                    },			
                    hideThumbs: 10,
                    fullWidth: "off",
                    fullScreen: "off",
                    fullScreenOffsetContainer: ""
                });
    }
    var revapi;
    if (jQuery('.tp-banner3').length) {
        revapi = jQuery('.tp-banner3').revolution(
                {
                    delay: 15000,
                    startwidth: 870,
                    startheight: 325,
                    autoHeight: "off",
                    navigationType: "none",
                    hideThumbs: 10,
                    fullWidth: "on",
                    fullScreen: "off",
                    fullScreenOffsetContainer: ""
                });
    }
    if (typeof revolution !== 'undefined' && $.isFunction(revolution)) {
        if (jQuery('.tp-banner4').length) {
            revapi = jQuery('.tp-banner4').revolution(
                    {
                        delay: 15000,
                        startwidth: 1170,
                        startheight: 455,
                        autoHeight: "off",
                        navigationType: "none",
                        hideThumbs: 10,
                        fullWidth: "on",
                        fullScreen: "off",
                        fullScreenOffsetContainer: ""
                    });
        }
    }
    $(".amount-btns a").live('click', function () {
        $(".amount-btns a").removeClass('selected');
        var amount = $(this).children('span').html();
        $(this).addClass('selected');
        $('.other-amount #textfield').val(amount);
    });
    $(".recursive-periods a").live('click', function () {
        $(".recursive-periods a").removeClass('selected');
        var time_period = $(this).html();
        var currency = $(this).data('currency');
        var symbol = $(this).data('symbol');
        var don_name = jQuery('#donner_name').val();
        var don_email = jQuery('#donner_email').val();
        $(this).addClass('selected');
        if (time_period != 'One Time') {
            $('.loading').show();
            var data = {
                'action': 'getbutton', //calls wp_ajax_nopriv_getbutton
                'period': time_period,
                'symbol': symbol,
                'currency': currency
            };
            $.post(ajaxurl, data, function (responce) {
                $('.other-amount.paypal').html(responce);
                $('div.amount-btns a').each(function ($) {
                    jQuery(this).removeClass('selected');
                });
                if (time_period == "Weekly") {
                    $('#billing-period').val('Week');
                    $('#billing-frequency').val('1');
                    $('#donor_name').val(don_name);
                    $('#donor_email').val(don_email);
                } else if (time_period == "Daily") {
                    $('#billing-period').val('Day');
                    $('#billing-frequency').val('1');
                    $('#donor_name').val(don_name);
                    $('#donor_email').val(don_email);
                } else if (time_period == "Fortnightly") {
                    $('#billing-period').val('SemiMonth');
                    $('#billing-frequency').val('1');
                    $('#donor_name').val(don_name);
                    $('#donor_email').val(don_email);
                } else if (time_period == "Monthly") {
                    $('#billing-period').val('Month');
                    $('#billing-frequency').val('1');
                    $('#donor_name').val(don_name);
                    $('#donor_email').val(don_email);
                } else if (time_period == "Quarterly") {
                    $('#billing-period').val('Month');
                    $('#billing-frequency').val('3');
                    $('#donor_name').val(don_name);
                    $('#donor_email').val(don_email);
                } else if (time_period == "Half year") {
                    $('#billing-period').val('Month');
                    $('#billing-frequency').val('6');
                    $('#donor_name').val(don_name);
                    $('#donor_email').val(don_email);
                } else if (time_period == "Yearly") {
                    $('#billing-period').val('Year');
                    $('#billing-frequency').val('1');
                    $('#donor_name').val(don_name);
                    $('#donor_email').val(don_email);
                }
                $('.loading').hide();
            });
            if ($('form#login').length > 0) {
                $('.other-amount').css('display', 'none');
                $('form#login').css('display', 'block');
            }
        } else {
            $('div.amount-btns a').each(function ($) {
                jQuery(this).removeClass('selected');
            });
            $('.loading').show();
            var data = {
                'action': 'getbutton',
                'symbol': symbol,
                'currency': currency,
                'period': 'one-time'
            };
            $.post(ajaxurl, data, function (responce) {
                $('.other-amount.paypal').html(responce);
                $('.other-amount.paypal').fadeIn();
                $('.loading').hide();
            });
        }
    });
    $("#paypal_confirmation").on('click', function () {
        $('.loading').show();
        var data = {
            'action': 'confirm_order'
        };
        $.post(ajaxurl, data, function (responce) {
            $('.donate-popup').html(responce);
            $('.loading').hide();
        });
        return false;
    });
    if ($('.loading').length === 0) {
        $('body').append('<div class="loading" style="display:none;"></div>');
    }
//    $(".donate-drop-btn").click(function () {
//        $(".donate-drop-down").slideToggle();
//        $(this).toggleClass('down');
//
//    });

});     





function header_counter(name, date) {
    var austDay = new Date();
    austDay = new Date(date);
    console.log(austDay);
    jQuery(name).countdown({
        until: austDay,
        format: 'HMS'
    });
}
function counter_inner(name, dated) {
    jQuery("." + name).downCount({
        date: '+dated+'
    });
}
jQuery(window).load(function ($) {
    //jQuery('section.no-container').parent().parent().parent().parent().addClass('gray no-container');
});
jQuery(document).ready(function ($) {
    $('form#credit_card_form').live('submit', function (event) {
        jQuery('div.loading').fadeIn(500);
        var error = false;
        $('#submitBtn').attr("disabled", "disabled");
        var ccNum = $('#card_number').val();
        var cvcNum = $('#cvc').val();
        var expMonth = $('#card-mnth').val();
        var expYear = $('#card-year').val();
        var amount = jQuery('#amount').val();
        if (!Stripe.card.validateCVC(cvcNum)) {
            jQuery('div.loading').fadeOut(500);
            error = true;
            reportError(c_crd_cvc);
        }
        if (!Stripe.card.validateCardNumber(ccNum)) {
            jQuery('div.loading').fadeOut(500);
            error = true;
            reportError(c_crd_no);
        }
        if (amount == "") {
            error = true;
            reportError(c_amount);
        }
        if (!error) {
            Stripe.card.createToken({
                number: ccNum,
                cvc: cvcNum,
                exp_month: expMonth,
                exp_year: expYear
            }, stripeResponseHandler);
        }
        return false;
    });
});
function reportError(msg) {
    if (msg.match('Successfully') != null) {
        jQuery('#payment-errors').text(msg).addClass('alert alert-success');
    } else {
        jQuery('#payment-errors').text(msg).addClass('alert alert-error');
    }
    jQuery('#submitBtn').prop('disabled', false);
    return false;
}
function stripeResponseHandler(status, response) {
    if (response.error) {
        reportError(response.error.message);
    } else {
        var f = jQuery("#credit_card_form");
        var token = response['id'];
        f.append("<input id='card_tocken' type='hidden' name='stripeToken' value='" + token + "' />");
        //f.get(0).submit();
        var ccNum = jQuery('#card_number').val();
        var cvcNum = jQuery('#cvc').val();
        var expMonth = jQuery('#card-mnth').val();
        var expYear = jQuery('#card-year').val();
        var tocken = jQuery('#card_tocken').val();
        var amount = jQuery('#amount').val();
        var don_name = jQuery('#donner_name').val();
        var don_email = jQuery('#donner_email').val();
        var currency_code = jQuery('div.donate-popup').find('a').data('symbol');
        var data = 'don_name=' + don_name + '&don_email=' + don_email + '&currency=' + currency_code + '&card_num=' + ccNum + '&card_cvc=' + cvcNum + '&exp_mth=' + expMonth + '&exp_year=' + expYear + '&token=' + tocken + '&amount=' + amount + '&action=sh_credit_card_process';
        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            data: data,
            beforeSend: function () {
                jQuery('div.loading').fadeIn(500);
            },
            success: function (credi_respnse) {
                jQuery("#payment-errors").empty();
                reportError(credi_respnse);
                jQuery('div.loading').fadeOut(500);
            }
        });
    }
    return false;
}
jQuery(document).ready(function ($) {
    $("a.btn-don, span.btn-don, div.btn-don, div.btn-don, .donate-drop-btn").on('click', function () {
        var types = $(this).data('type');
        var url = $(this).data('url');
        if (types == 'general') {
            var data = 'url=' + url + '&action=sh_donation_popup_ajax';
        } else if (types == 'post') {
            var id = $(this).data('id');
            var data = 'url=' + url + '&id=' + id + '&types=' + types + '&action=sh_donation_popup_ajax';
        } else if (types == 'project') {
            var id = $(this).data('id');
            var data = 'url=' + url + '&id=' + id + '&types=' + types + '&action=sh_donation_popup_ajax';
        }
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function () {
                jQuery('div.loading').fadeIn(500);
            },
            success: function (res) {
                $('div#myModal').html(res);
                jQuery('div.loading').fadeOut(500);
                $('div#myModal').modal('show');
            }
        });
        return false;
    });
    return false;
});
jQuery(document).ready(function ($) {
    $('form').find('button.donate-btn').live('click', function () {
        if ($(this).attr('name') == 'undefined') {
            var don_name = jQuery('#donner_name').val();
            var don_email = jQuery('#donner_email').val();
            var data = 'don_name=' + don_name + '&don_email=' + don_email + '&action=sh_paypal_donner_before';
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    jQuery('div.loading').fadeIn(500);
                },
                success: function (res) {
                    jQuery('div.loading').fadeOut(500);
                    if (res == '1') {
                        $('form').find('button.donate-btn').parent().eq(0).submit();
                    }
                }
            });
            return false;
        }
    });
    $('div.credit-card-options input[name=donner_name]').live('keyup change paste', function (e) {
        var data = $(this).val();
        var correct_form = $('form').find('button.donate-btn');
        var get_field = $(correct_form).attr('name');
        if (get_field == 'recurring_pp_submit') {
            var field = $(correct_form).prev().prev().prev().prev();
            $(correct_form).prev().prev().prev().prev().attr('value', data);
            e.preventDefault();
        }
    });
    $('div.credit-card-options input[name=donner_email]').live('keyup change paste', function (e) {
        var data = $(this).val();
        var correct_form = $('form').find('button.donate-btn');
        var get_field = $(correct_form).attr('name');
        if (get_field == 'recurring_pp_submit') {
            $(correct_form).prev().prev().prev().attr('value', data);
            e.preventDefault();
        }
    });
});
jQuery(document).ready(function ($) {
    $('form#checkout2_form input#submitBtn').live('click', function () {
        var parent = $('form#checkout2_form');
        var button = $(this);
        $(this).prop('disabled', true);
        jQuery('div.loading').fadeIn('slow');
        TCO.loadPubKey(CHECKOUT2_PUBLIC_KEY_TYPE, function () {
            if (CHECKOUT2_PUBLIC_KEY_TYPE != '')
            {
                var top_fields = sh_2_fields();
                if (top_fields.length === 0) {
                    var fields_output = sh_form_fields_check();
                    if (fields_output.length === 0) {
                        var errorCallback = function (data) {
                            if (data.errorCode !== 200) {
                                $('div#payment-errors').empty();
                                $('div.loading').fadeOut('slow');
                                $('div#payment-errors').html('<div class="alert alert-warning">' + data.errorMsg + '</div>');
                                $('div#payment-errors').ScrollTo({offsetTop: 100});
                                setTimeout(function () {
                                    $('div#payment-errors').fadeOut('slow');
                                }, 5000);
                                $(button).prop('disabled', false);
                                return false;
                            }
                        };
                        var successCallback = function (data) {
                            $('div.loading').fadeIn('slow');
                            jQuery('<input>', {
                                'type': 'hidden',
                                'name': 'checkout2Token',
                                'value': data.response.token.token,
                                'id': 'checkout2_payment_access_tocken'
                            }).appendTo(parent);
                            $('div.loading').fadeIn('slow');
                            var parent_ = jQuery('.other-amount.donner.credit-card-options');
                            var name = jQuery(parent_).find('#donner_name').val();
                            var mail = jQuery(parent_).find('#donner_email').val();
                            var token = data.response.token.token;
                            var amoutn = jQuery(parent).find('#amount').val();
                            var address = jQuery(parent).find('#chekcout2_address').val();
                            var city = jQuery(parent).find('#chekcout2_city').val();
                            var state = jQuery(parent).find('#chekcout2_state').val();
                            var zipcode = jQuery(parent).find('#chekcout2_zip_code').val();
                            var phone_no = jQuery(parent).find('#chekcout2_contact_no').val();
                            var country = jQuery(parent).find('#checkout2_country').val();
                            var action = 'sh_2checkout_tocken_process';
                            var data = 'country=' + country + '&name=' + name + '&mail=' + mail + '&token=' + token + '&amoutn=' + amoutn + '&address=' + address + '&city=' + city + '&state=' + state + '&zipcode=' + zipcode + '&phone_no=' + phone_no + '&action=sh_2checkout_tocken_process';
                            $.ajax({
                                type: "POST",
                                url: ajaxurl,
                                data: data,
                                cache: false,
                                beforeSend: function () {
                                    jQuery('div.loading').fadeIn('slow');
                                },
                                success: function (response) {
                                    $(button).prop('disabled', false);
                                    jQuery('div.loading').fadeOut('slow');
                                    $('div#payment-errors').empty();
                                    $('div#payment-errors').show();
                                    $('div#payment-errors').html('<div class="alert alert-info">' + response + '</div>');
                                    $('div#payment-errors').ScrollTo({offsetTop: 100});
                                }
                            });
                        };
                        var card_num = jQuery(parent).find('#card_number').val();
                        var card_exp_mth = jQuery(parent).find('#card-mnth').val();
                        var card_exp_year = jQuery(parent).find('#card-year').val();
                        var card_cvc = jQuery(parent).find('#cvc').val();
                        if ($('input#checkout2_payment_access_tocken').length && $('input#checkout2_payment_access_tocken').val().length) {
                            $('div#payment-errors').empty();
                            $('div#payment-errors').show();
                            $('div#payment-errors').html('<div class="alert alert-info">Token Time Out. Please Start from Over</div>');
                            $('div#payment-errors').ScrollTo({offsetTop: 100});
                            setTimeout(function () {
                                $('div#payment-errors').fadeOut('slow');
                            }, 10000);
                            $(button).prop('disabled', false);
                        } else {
                            var args = {
                                sellerId: CHECKOUT2_Account_No,
                                publishableKey: CHECKOUT2_PUBLIC_KEY,
                                ccNo: card_num,
                                cvv: card_cvc,
                                expMonth: card_exp_mth,
                                expYear: card_exp_year
                            };
                            TCO.requestToken(successCallback, errorCallback, args);
                        }
                    } else {
                        $(button).prop('disabled', false);
                        $('div#payment-errors').empty();
                        jQuery('div.loading').fadeOut('slow');
                        $(fields_output).each(function (i, v) {
                            $('div#payment-errors').append(v);
                        });
                        $('div#payment-errors').show();
                        $('div#payment-errors').ScrollTo({offsetTop: 100});
                        setTimeout(function () {
                            $('div#payment-errors').fadeOut('slow');
                        }, 10000);
                    }
                } else {
                    $(button).prop('disabled', false);
                    $('div#sh_to_errors').empty();
                    $(top_fields).each(function (i, v) {
                        $('div#sh_to_errors').append(v);
                    });
                    $('div#sh_to_errors').show();
                    jQuery('div.loading').fadeOut('slow');
                    $('div#sh_to_errors').ScrollTo({offsetTop: 100});
                    setTimeout(function () {
                        $('div#sh_to_errors').fadeOut('slow');
                    }, 10000);
                }
            } else {
                $(button).prop('disabled', false);
                jQuery('div.loading').fadeOut('slow');
                $('div#sh_to_errors').empty();
                $('div#sh_to_errors').html('<div class="alert alert-warning">Please Fill All Information in theme options that are relate with 2checkout</div>');
                $('div#sh_to_errors').show();
                console.log($('#sh_to_errors').offset().top);
                $('div#sh_to_errors').ScrollTo({offsetTop: 100});
                setTimeout(function () {
                    $('div#sh_to_errors').fadeOut('slow');
                }, 10000);
            }
        });
        jQuery('#wp-appointment-overlap .loader').fadeOut('slow');
        jQuery('#wp-appointment-overlap').fadeOut('slow');
        return false;
    });
    // braintree gateway
    $('form#braintree_form input#submitBtn').live('click', function () {
        var parent = $('form#braintree_form');
        var button = $(this);
        $(this).prop('disabled', true);
        $('div.loading').fadeIn('slow');
        var top_fields = sh_2_fields();
        var fields_output = sh_form_fields_check_braintree();
        if (top_fields.length === 0) {
            if (fields_output.length === 0) {
                var parent_ = jQuery('.other-amount.donner.credit-card-options');
                var name = $(parent_).find('#donner_name').val();
                var email = $(parent_).find('#donner_email').val();
                var card_num = $(parent).find('#card_number').val();
                var card_exp_mth = $(parent).find('#card-mnth').val();
                var card_exp_year = $(parent).find('#card-year').val();
                var card_cvc = $(parent).find('#cvc').val();
                var ammount = $(parent).find('input#amount').val();
                var data = 'name=' + name + '&email=' + email + '&card_num=' + card_num + '&mnth=' + card_exp_mth + '&exp_year=' + card_exp_year + '&cvc=' + card_cvc + '&amount=' + ammount + '&action=sh_braintree_tocken_process';
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: data,
                    beforeSend: function () {
                        $('div.loading').fadeIn('slow');
                    },
                    success: function (response) {
                        $(button).prop('disabled', false);
                        jQuery('div.loading').fadeOut('slow');
                        $('div#payment-errors').empty();
                        $('div#payment-errors').show();
                        $('div#payment-errors').html('<div class="alert alert-info">' + response + '</div>');
                        $('div#payment-errors').ScrollTo({offsetTop: 100});
                    }
                });
            } else {
                $(button).prop('disabled', false);
                $('div#payment-errors').empty();
                $(fields_output).each(function (i, v) {
                    $('div#payment-errors').append(v);
                });
                $('div#payment-errors').show();
                $('div.loading').fadeOut('slow');
                $('div#payment-errors').ScrollTo({offsetTop: 100});
                setTimeout(function () {
                    $('div#payment-errors').fadeOut('slow');
                }, 10000);
            }
        } else {
            $(button).prop('disabled', false);
            $('div#sh_to_errors').empty();
            $(top_fields).each(function (i, v) {
                $('div#sh_to_errors').append(v);
            });
            $('div#sh_to_errors').show();
            jQuery('div.loading').fadeOut('slow');
            $('div#sh_to_errors').ScrollTo({offsetTop: 100});
            setTimeout(function () {
                $('div#sh_to_errors').fadeOut('slow');
            }, 10000);
        }
        return false;
    });
   
});
function sh_form_fields_check() {
    var parent = jQuery('form#checkout2_form');
    var amoutn = jQuery(parent).find('#amount').val();
    var address = jQuery(parent).find('#chekcout2_address').val();
    var city = jQuery(parent).find('#chekcout2_city').val();
    var state = jQuery(parent).find('#chekcout2_state').val();
    var zipcode = jQuery(parent).find('#chekcout2_zip_code').val();
    var phone_no = jQuery(parent).find('#chekcout2_contact_no').val();
    var card_no = jQuery(parent).find('#card_number').val();
    var cvc = jQuery(parent).find('#cvc').val();
    var msg = [];
    if (amoutn === '') {
        msg.push('<div class="alert alert-warning">Please Enter or Select Donation Ammount</div>');
    }
    if (address === '') {
        msg.push('<div class="alert alert-warning">Please Enter Address</div>');
    }
    if (city === '') {
        msg.push('<div class="alert alert-warning">Please Enter your City</div>');
    }
    if (state === '') {
        msg.push('<div class="alert alert-warning">Please Enter your state</div>');
    }
    if (zipcode === '') {
        msg.push('<div class="alert alert-warning">Please Enter your Zip Code</div>');
    }
    if (phone_no === '') {
        msg.push('<div class="alert alert-warning">Please Enter your Contact Number</div>');
    }
    if (card_no === '') {
        msg.push('<div class="alert alert-warning">Please Enter your Credit Card Number</div>');
    }
    if (cvc === '') {
        msg.push('<div class="alert alert-warning">Please Enter your Card CVC Number</div>');
    }
    return msg;
}
function sh_form_fields_check_braintree() {
    var parent = jQuery('form#braintree_form');
    var amoutn = jQuery(parent).find('#amount').val();
    var card_no = jQuery(parent).find('#card_number').val();
    var cvc = jQuery(parent).find('#cvc').val();
    var msg = [];
    if (amoutn === '') {
        msg.push('<div class="alert alert-warning">Please Enter or Select Donation Ammount</div>');
    }
    if (card_no === '') {
        msg.push('<div class="alert alert-warning">Please Enter your Credit Card Number</div>');
    }
    if (cvc === '') {
        msg.push('<div class="alert alert-warning">Please Enter your Card CVC Number</div>');
    }
    return msg;
}
function sh_2_fields() {
    var parent = jQuery('.other-amount.donner.credit-card-options');
    var name = jQuery(parent).find('#donner_name').val();
    var mail = jQuery(parent).find('#donner_email').val();
    var msg = [];
    if (name === '') {
        msg.push('<div class="alert alert-warning">Please Enter Your Name</div>');
    }
    if (mail === '') {
        msg.push('<div class="alert alert-warning">Please Enter Your email</div>');
    } else if (validateEmail(mail) !== true) {
        msg.push('<div class="alert alert-warning">Please Enter Correct Email</div>');
    }
    return msg;
}
function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}

jQuery(window).load(function(){
 jQuery(".site-loading").fadeOut();
});
