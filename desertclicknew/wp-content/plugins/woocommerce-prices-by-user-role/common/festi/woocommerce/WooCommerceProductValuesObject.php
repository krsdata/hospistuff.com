<?php

class WooCommerceProductValuesObject
{
    private $_post;
    private $_product;
    
    public function __construct($post)
    {
        $this->_post = $post;
        
        $factory = new WC_Product_Factory();
        $this->_product = $factory->get_product($this->getID());
    } // end __construct
    
    public function getID()
    {
        return $this->_post->ID;
    } // end getID
    
    public function getType()
    {
        return $this->_product->product_type;
    } // end getType
}