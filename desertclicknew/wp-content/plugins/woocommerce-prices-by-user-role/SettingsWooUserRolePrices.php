<?php
class SettingsWooUserRolePrices
{
    private $_languageDomain = '';

    public function __construct($languageDomain)
    {
        $this->_languageDomain = $languageDomain;
    } // end __construct

    public function get()
    {
        $settings = array(
            'hideAddToCartButton' => array(
                'caption' => __(
                    'Hide the &quot;Add to Cart&quot; Button',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'fieldsetKey' => 'general',
                'classes' => 'festi-case-hide-add-to-cart-button',
                'lable' => __(
                    'Enable hidden the &quot;Add to Cart&quot; button from non-registered users',
                    $this->_languageDomain
                ),
            ),
            'textForNonRegisteredUsers' => array(
                'caption' => __(
                    'Text for Non-Registered Users instead of <br>'.
                    '&quot;Add to Cart&quot; button',
                    $this->_languageDomain
                ),
                'type' => 'textarea',
                'fieldsetKey' => 'general',
                'classes' => 'festi-case-text-instead-button-for-non-registered-users'
            ),
            'hideAddToCartButtonForUserRoles' => array(
                'caption' => __(
                    'Hide the &quot;Add to Cart&quot; Button <br /> from Roles',
                    $this->_languageDomain
                ),
                'type' => 'multicheck',
                'default' => array(),
                'fieldsetKey' => 'general',
                'deleteButton' => false,
                'classes' => 'festi-hint-upper',
                'hint' => __(
                    'Enable hidden the &quot;Add to Cart&quot; button from certain user roles',
                    $this->_languageDomain
                ),
            ),
            'onlyRegisteredUsers' => array(
                'caption' => __(
                    'Show the Prices only to Registered Users',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border festi-case-only-registered-users',
                'lable' => __(
                    'Enable hidden prices for all products from non-registered users',
                    $this->_languageDomain
                ),
            ),
            'textForUnregisterUsers' => array(
                'caption' => __(
                    'Text for Non-Registered Users',
                    $this->_languageDomain
                ),
                'type' => 'textarea',
                'default' => __(
                    'Please login or register to see price',
                    $this->_languageDomain
                ),
                'fieldsetKey' => 'general',
                'classes' => 'festi-case-text-for-unregistered-users',
                'hint' => __(
                    'Provide written text for non-registered users which '.
                    'will be displayed on instead of the price',
                    $this->_languageDomain
                ),
            ),
            'hidePriceForUserRoles' => array(
                'caption' => __(
                    'Hide the Prices from Roles',
                    $this->_languageDomain
                ),
                'type' => 'multicheck',
                'default' => array(),
                'fieldsetKey' => 'general',
                'deleteButton' => false,
                'classes' => 'festi-user-role-prices-top-border festi-case-hide-price-for-user-roles',
                'hint' => __(
                    'Enable hidden prices from certain user roles',
                    $this->_languageDomain
                ),
            ),
            'textForRegisterUsers' => array(
                'caption' => __(
                    'Text for Registered Users with <br /> Hidden Price',
                    $this->_languageDomain
                ),
                'type' => 'textarea',
                'default' => __(
                    'Price for your role is hidden',
                    $this->_languageDomain
                ),
                'fieldsetKey' => 'general',
                'classes' => 'festi-case-text-for-registered-users festi-hint-upper',
                'hint' => __(
                    'Provide written text for registered users with certain '.
                    'roles which will be shown instead of the product price',
                    $this->_languageDomain
                ),
            ),
            'discountOrMakeUp' => array(
                'caption' => __(
                    'Discount or Markup for Products',
                    $this->_languageDomain
                ),
                'type' => 'input_select',
                'values' => array(
                    'discount' => __('discount', $this->_languageDomain),
                    'markup' => __('markup',$this->_languageDomain)
                ),
                'default' => 'discount',
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border',
                'hint' => __(
                    'Provide discount or markups in fixed or percentage terms for all products on shop',
                    $this->_languageDomain
                ),
            ),
            'discountByRoles' => array(
                'caption' => __(
                    '',
                    $this->_languageDomain
                ),
                'type' => 'multidiscount',
                'default' => array(),
                'fieldsetKey' => 'general',
                'deleteButton' => false,
            ),
            'roles' => array(
                'caption' => __(
                    'User Roles for Special Pricing',
                    $this->_languageDomain
                ),
                'type' => 'multicheck',
                'default' => array(),
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border',
                'deleteButton' => true,
                'hint' => __(
                    'Select user roles which should be active on '.
                    'product page for special prices',
                    $this->_languageDomain
                ),
            ),
            'showCustomerSavings' => array(
                'caption' => __(
                    'Display Price Savings on',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Display to customer regular price, the user role price '.
                    'with label &quot;Your Price&quot;, and the percent saved '.
                    'with label &quot;Savings&quot;',
                    $this->_languageDomain
                ),
                'type' => 'multi_select',
                'values' => array(
                    'product' => __('Product Page', $this->_languageDomain),
                    'archive' => __(
                        'Products Archive Page (for Simple product)',
                        $this->_languageDomain
                    ),
                    'cartTotal' => __(
                        'Cart Page (for Order Total)',
                        $this->_languageDomain
                    ),
                ),
                'default' => array(),
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border'
            ),
            'customerSavingsLableColor' => array(
                'caption' => __(
                    'Color for Savings Labels',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'fieldsetKey' => 'general',
                'default' => '#ff0000',
                'eventClasses' => 'showCustomerSavings',
                'hint' => __(
                    'Select color for text labels about customer savings '.
                    'Regular Price, Your Price, Savings',
                    $this->_languageDomain
                ),
            ),
        );

        return $settings;
    } // end get
}