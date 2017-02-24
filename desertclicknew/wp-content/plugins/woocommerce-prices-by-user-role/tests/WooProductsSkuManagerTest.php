<?php
require_once dirname(__FILE__).'/PriceByRoleTestCase.php';

class WooProductsSkuManagerTest extends PriceByRoleTestCase
{
       
    public $skuManager;
    
    public function setUp()
    {
        parent::setUp();
        
        $file = 'WooProductsSkuManager.php';
        require_once $this->getPluginPath('common/import/'.$file);
        $this->doInitSkuManager();
        $this->doCreateProduct();
    }
    
    public function doInitSkuManager()
    {
        $engine = 'testEngine';
        $config = 'testConfig';
        $language = 'testDomain';
        
        $this->skuManager = new WooProductsSkuManager(
            $config,
            $engine,
            $language
        ); 
    }
    
    public function getTestingPost($id) 
    {
        return get_post($id);
    }
    
    public function doCreateProduct()
    {
        parent::doCreateProduct();
        
        $id = $this->getProductId('simple');
        update_post_meta($id, '_stock_status', 'outofstock');
        
        $this->skuManager->exsistProduct = $this->getTestingPost($id);
    }
    
    public function getTestCases()
    {
        $cases = array(
        // _stock_status option exists in imported file
            array(
                'option'   => '_stock_status',
                'value'    => 'instock',
                'expected' => 'instock'
            ), 
        // _stock option exists in imported file
            array(
                'option'   => '_stock',
                'value'    => '10',
                'expected' => 'instock'
            ),
        // no options in imported file, get option from exsiting post
            array(
                'option'   => '',
                'expected' => 'outofstock'
            )
        );
        
        return $cases;
    }

    public function isOptionExsits($value)
    {
        return !empty($value);
    }

    public function setCaseOptions($case)
    {
        $this->doResetOptions();
        
        if ($this->isOptionExsits($case['option'])) {
            $key = $case['option'];
            $value = $case['value'];    
                
            $this->skuManager->newPostMeta[$key] = $value;
        }
    }
    
    public function doResetOptions()
    {
        $options = array(
            '_stock_status',
            '_stock'
        );
        
        foreach ($options as $option) {
            unset($this->skuManager->newPostMeta[$option]);
        }
    }
    
    public function testSetStockStatusForProduct()
    {
        $cases = $this->getTestCases();
        
        foreach ($cases as $case) {
            $this->setCaseOptions($case);
            
            $testedValue = $this->skuManager->setStockStatusForProduct();
            
            $this->assertEquals($case['expected'], $testedValue);
        } 
    }
    
    /**
     * @ticket 2674 http://localhost.in.ua/issues/2674
    */
    public function testUpdateProductImageGallery()
    {
        $product = WC_Helper_Product::create_simple_product();
        $imageGalleryIds = array(1, 2, 3, 4, 5);
        $meta_value = implode(',', $imageGalleryIds);
        $sku = '999-B';
        update_post_meta($product->id, '_product_image_gallery', $meta_value);
        update_post_meta($product->id, '_sku', '999-X');
           
        $language = 'testDomain';

        $options = $this->_getPrepareCsvOptions();
        
        $pluginInstance = $this->getBackendPluginInstance();
        
        $engine = $pluginInstance->getImportManager()->doProcessing($options);
        
        $skuManager = new WooProductsSkuManager(
            $options,
            $engine,
            $language
        ); 
        
        $importImageGalleryIds = array();
        
        foreach ($engine as $key => $item) {
            $skuManager->newPostId = $item['post_id'];
            $skuManager->updateProductImageGallery($importImageGalleryIds);
        }
        
        $updateImageGallery = get_post_meta(
            $product->id,
            '_product_image_gallery',
            true
        );
        
        $updateImageGallery = explode(',', $updateImageGallery);
        
        $this->assertTrue($imageGalleryIds == $updateImageGallery);
    }
    
    /**
     * @ticket 2676 http://localhost.in.ua/issues/2676
    */
    public function testDoSyncVariationProduct()
    {         
        $product = WC_Helper_Product::create_simple_product();
  
        $language = 'testDomain';
        $sku = '999-X';
        $testAttributes['size'] = array(
            'name'         => 'size',
            'value'        => '15|15.5',
            'is_visible'   => '1',
            'is_variation' => '1',
            'is_taxonomy'  => '0',
            'is_visible'   => '1' 
        );
        
        update_post_meta($product->id, '_product_attributes', $testAttributes);
        update_post_meta($product->id, '_sku', $sku);
        
        $pluginInstance = $this->getBackendPluginInstance();
        
        $fileName ='import-variations-attributes-sku.csv';
        
        $options = array(
            'offset' => 0,
            'isFirstRowHeader' => 1,
            'filePath' => $this->_getCsvFilePath($fileName),
            'csvSeparator' => ',',
            'categorySeparator' => '/',
            'decimalSeparator' => ',',
            'mapTo' => array(
                0 => 'post_title',
                1 => '_sku',
                2 => '_parent_sku',
                3 => '_regular_price'
            ),
            'custom_field_name' => array(
                0 => 'Product Title',
                1 => 'SKU',
                2 => 'Parent SKU',
                3 => 'Price'
            )
        );
        
        $engine = $pluginInstance->getImportManager()->doProcessing($options);
        
        $productAttributesAfterSync = get_post_meta(
            $product->id,
            '_product_attributes',
            true
        );
        
        $this->assertTrue($testAttributes == $productAttributesAfterSync);
    }
    
    private function _getWooProductValuesObject($sku)
    {
        $file = 'WooCommerceProductValuesObject.php';
        require_once $this->getPluginPath(
            'common/festi/woocommerce/'.$file
        );  
            
        $existingPostQuery = array(
            'numberposts' => 1,
            'meta_key'    => '_sku',
            'post_type'   => 'product',
            'meta_query'  => array(
                array(
                    'key'     =>'_sku',
                    'value'   => $sku,
                    'compare' => '='
                )
            )
        );
        
        $posts = get_posts($existingPostQuery);
        
        return new WooCommerceProductValuesObject($posts[0]);
    }
    
    private function _getPrepareCsvOptions()
    {
        $fileName ='import-variations-parent-sku.csv';
        
        $options = array(
            'offset' => 0,
            'isFirstRowHeader' => 1,
            'filePath' => $this->_getCsvFilePath($fileName),
            'csvSeparator' => ',',
            'categorySeparator' => '/',
            'decimalSeparator' => ',',
            'mapTo' => array(
                0 => 'post_title',
                1 => '_sku',
                2 => '_parent_sku',
                3 => '_regular_price',
                4 => 'custom_field',
                5 => 'post_content'
            ),
            'custom_field_name' => array(
                0 => 'Product Title',
                1 => 'SKU',
                2 => 'Parent SKU',
                3 => 'Price',
                4 => 'Color',
                5 => 'Description'
            )
        );
        
        return $options;
    }
    
    private function _getCsvFilePath($fileName)
    {
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'resources'.
               DIRECTORY_SEPARATOR.$fileName;
    } // end _getCsvFilePath
    
    /**
     * @ticket 2808 http://localhost.in.ua/issues/2808
    */
    public function testDoInsertAtachmentImage()
    {
        $this->_doPrepareProductBeforImport();
        $this->_doCreateImportCsv();
        
        $options = array(
            'offset' => 0,
            'isFirstRowHeader' => 1,
            'filePath' =>  $this->_csvFilePath,
            'csvSeparator' => ',',
            'categorySeparator' => '/',
            'decimalSeparator' => ',',
            'mapTo' => array(
                0 => '_sku',
                1 => 'post_title',
                2 => '_regular_price',
                3 => 'product_image_by_url'
            ),
            'custom_field_name' => array(
                0 => 'SKU',
                1 => 'Name',
                2 => 'Regular Price',
                3 => 'img URL'
            )
        );
        $pluginInstance = $this->getBackendPluginInstance();
        
        $engine = $pluginInstance->getImportManager()->doProcessing($options);
        
        unlink($this->_csvFilePath);
        
        $countPosts = $this->_getCountPostHasAtachment();
        
        $countReferencePosts = 2;
        
        $this->assertEquals($countReferencePosts, $countPosts);   
    }
    
    private function _doPrepareProductBeforImport()
    {
        $productOne = WC_Helper_Product::create_simple_product();
        $productTwo = WC_Helper_Product::create_simple_product();
        update_post_meta($productOne->id, '_sku', '119');    
        update_post_meta($productTwo->id, '_sku', '120');
        
        $imgePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'resources'.
                   DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.
                   'import-duplicate-1.jpg';
        
        $destImageUrl = $this->_getUrlWithPath($imgePath);
       
        $this->_doInsertPrepareAttachment($destImageUrl, $productOne->id);
    }
    
    private function _getUrlWithPath($path)
    {
        return str_ireplace(ABSPATH, home_url('/'), $path);
    }
    
    private function _doCreateImportCsv()
    {
        $path = dirname(__FILE__).DIRECTORY_SEPARATOR.
                   'resources'.DIRECTORY_SEPARATOR;
                   
        $imagePath = $path.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR;
        
        $fileFields = array(
            array('SKU', 'Name', 'Regular Price', 'img URL'),
            array('119', 'test9', '9', $imagePath.'import-duplicate-1.jpg'),
            array('120', 'test10', '11', $imagePath.'import-duplicate-2.jpg')
        );
        
        $fileName = 'import-duplicate-images.csv';
        $this->_csvFilePath = $path.$fileName;
        
        $fp = fopen($this->_csvFilePath, 'w');
        
        foreach ($fileFields as $field) {
            fputcsv($fp, $field);
        }
        
        fclose($fp);
    }
    
    private function _getCountPostHasAtachment()
    {
        $postsHasAttachement = array();
            
        $attr = array(
            'numberposts' => 5,
            'post_type' => 'attachment' 
        );
        $postsHasAttachement = get_posts($attr);
        
        return sizeof($postsHasAttachement);
    }
    
    private function _doInsertPrepareAttachment($path, $idProduct)
    {
        $attachment = array(
            'guid' => $path,
            'post_mime_type' => 'image/jpeg',
            'post_title' => 1,
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        wp_insert_attachment(
            $attachment,
            $path, 
            $idProduct
        );
    }
}
