jQuery(document).ready(function()
{
    hideOrShowBlockTextForUnregisteredUsers();
    hideOrShowBlockTextForRegisteredUsers();
    hideOrShowBlockTextInstedOfAddToCartButton();

    function hideOrShowBlockTextInstedOfAddToCartButton()
    {
        var selector = '.festi-case-text-instead-button-for-non-registered-users';

        if (jQuery('.festi-case-hide-add-to-cart-button input').is(":checked")) {
            jQuery(selector).show();
        } else {
            jQuery(selector).hide();
        }
    } // end hideOrShowBlockTextInstedOfAddToCartButton

    function hideOrShowBlockTextForUnregisteredUsers()
    {
        var selector = '.festi-case-text-for-unregistered-users';

        if (jQuery('.festi-case-only-registered-users input').is(":checked")) {
            jQuery(selector).show();
        } else {
            jQuery(selector).hide();
        }
    } // end hideOrShowBlockTextForUnregisteredUsers

    function hideOrShowBlockTextForRegisteredUsers()
    {
        var selector = '.festi-case-text-for-registered-users';

        if (jQuery('.festi-case-hide-price-for-user-roles input').is(":checked")) {
            jQuery(selector).show();
        } else {
            jQuery(selector).hide();
        }
    } // end hideOrShowBlockTextForRegisteredUsers

    jQuery('input[data-event=visible]').change(function(){

        var className = jQuery(this).attr("name") + '-' + jQuery(this).data('event');

        if(jQuery(this).attr("checked")){

            jQuery('.'+className).fadeIn();
        } else {
            jQuery('.'+className).fadeOut(100);
        }
    });

    jQuery('select[data-event=visible]').change(function(){
        var className = jQuery(this).attr("name") + '-' + jQuery(this).data('event');

        if(jQuery(this).val() == 'disable'){
            jQuery('.'+className).fadeOut(100);
        } else {
            jQuery('.'+className).fadeIn();
        }
    });

    jQuery('.festi-user-role-prices-delete-role').click(function()
    {
        if (!confirm('Are you sure to delete')) {
            return false;
        }
    });

    jQuery('#festi-user-role-prices-discount-roles input[type=number]').live('keypress', function(e)
    {
        if( e.which!=8 && e.which!=0 && e.which!=46 && (e.which<48 || e.which>57))
        {
            return false;
        }
    });

    jQuery('.festi-user-role-prices-help-tip').poshytip({
        className: 'tip-twitter',
        showTimeout:100,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'bottom',
        offsetY: 5,
        allowTipHover: false,
        fade: true,
        slide: false
    });


    jQuery('body').on('change', 'input[name="onlyRegisteredUsers"]', function() {
        hideOrShowBlockTextForUnregisteredUsers();
    });

    jQuery('body').on('change', 'input[name^="hidePriceForUserRoles"]', function() {
        hideOrShowBlockTextForRegisteredUsers();
    });

    jQuery('body').on('change', 'input[name="hideAddToCartButton"]', function() {
        hideOrShowBlockTextInstedOfAddToCartButton();
    });

}); 