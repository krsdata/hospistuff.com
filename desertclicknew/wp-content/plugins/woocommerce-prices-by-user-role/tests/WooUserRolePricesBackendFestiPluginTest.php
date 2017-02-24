<?php
require_once dirname(__FILE__).'/PriceByRoleTestCase.php';

class WooUserRolePricesBackendFestiPluginTest extends PriceByRoleTestCase
{
    private $_productOptionsHookName = 'woocommerce_product_options_pricing';
    private $_listenerMethodName = 'onAppendFieldsToSimpleOptionsAction';
    private $_listenerPriority = 10;
    private $_settingsOptionsKey = 'settings';
    
    public function setUp()
    {  
        parent::setUp();
        
        require_once $this->getPluginPath('WooUserRolePricesFestiPlugin.php');
        
        $file = 'WooUserRolePricesBackendFestiPlugin.php';
        require_once $this->getPluginPath($file);

        $this->_backend = new WooUserRolePricesBackendFestiPlugin(
            $this->pluginMainFile
        );
        $this->_backend->onInstall();
        $this->doClearCache();
        $this->doCreateProduct();
    } // end setUp
    
    public function testDuplicatingPricesFields()
    {
        $this->updateOptions(
            PRICE_BY_ROLE_OPTIONS_PREFIX.$this->_settingsOptionsKey,
            array(
                'roles' => array(
                    'administrator' => true
                )
            )
        );
        
        $path = $this->getWooCommercePath(
            'includes/admin/wc-meta-box-functions.php'
        );
        
        require_once $path;

        add_filter(
            $this->_productOptionsHookName,
            array($this->_backend, $this->_listenerMethodName),
            $this->_listenerPriority
        );
        
        $productId = $this->getProductId('simple');

        $GLOBALS['post'] = get_post($productId);
        
        $listenersList = $this->_getListenersOfWoocommerceAction();
        ob_start();
        $this->_backend->onAppendFieldsToSimpleOptionsAction();
        ob_get_clean();

        $result = $this->_hasPluginListenerOfPrintPriceFieldAction();
        
        $this->assertTrue($result);
    } // end testDuplicatingPricesFields
    
    private function _getListenersOfWoocommerceAction()
    {
        $hookName = $this->_productOptionsHookName;
        $listenersList =  $GLOBALS['wp_filter'][$hookName];

        return $listenersList;
    } // end _getListenersOfWoocommerceAction
    
    private function _hasPluginListenerOfPrintPriceFieldAction()
    {
        $listenersList = $this->_getListenersOfWoocommerceAction();
        
        //return $listenersList == false;
        // FIXME:
        return count($listenersList) == 1;
    } // end _hasPluginListenerOfPrintPriceFieldAction    
    
    public function testEmptyRolePricesProductMetaOptionsInUpdateAction()
    {
        $productId = $this->getProductId('simple');
        $this->_backend->onUpdateAllTypeProductMetaOptionsAction($productId);
        
        $rolePriceList = get_post_meta(
            $productId,
            PRICE_BY_ROLE_PRICE_META_KEY,
            true
        );
        
        $rolePriceList = json_decode($rolePriceList, true);
        
        $result = is_array($rolePriceList) && empty($rolePriceList);
        
        $this->assertTrue($result);
    } // end testEmptyRolePricesProductMetaOptionsInUpdateAction
    
    public function testCreateRoleWithUnacceptableCharacters()
    {
        try {
            $this->_backend->doAppendNewRoleToWordpressRolesList();
            $this->fail(
                "Expected exception ".PRICE_BY_ROLE_EXCEPTION_EMPTY_VALUE.
                " not thrown"
            );
        } catch (Exception $exp) {
            $this->assertTrue(
                $exp->getCode() == PRICE_BY_ROLE_EXCEPTION_EMPTY_VALUE
            );
        }
        
        try {
            $_POST['roleName'] = 'Группа 1';
            $this->_backend->doAppendNewRoleToWordpressRolesList();
            $this->fail(
                "Expected exception ".PRICE_BY_ROLE_EXCEPTION_INVALID_VALUE.
                " not thrown"
            );
        } catch (Exception $exp) {
            $this->assertTrue(
                $exp->getCode() == PRICE_BY_ROLE_EXCEPTION_INVALID_VALUE
            );
        }
        
        $_POST['roleName'] = 'Группа 1';
        $_POST['roleIdent'] = 'group1';
        $_POST['active'] = 1;
        $this->_backend->doAppendNewRoleToWordpressRolesList();
        
        $roles = $this->_backend->getActiveRoles();
        
        $this->assertArrayHasKey($_POST['roleIdent'], $roles);
    } // end testCreateRoleWithUnacceptableCharacters
    
    /**
     * @ticket 2710 http://localhost.in.ua/issues/2710
     */
    public function testCompareVersion()
    {
        require_once $this->getPluginPath('WooUserRolePricesUtils.php');
        
        try {
            WooUserRolePricesUtils::doCheckPhpVersion('10.0.0');
            $this->fail(
                "Expected exception ".
                PRICE_BY_ROLE_EXCEPTION_INVALID_PHP_VERSION." not thrown"
            );
        } catch (Exception $exp) {
            $this->assertTrue(
                $exp->getCode() == PRICE_BY_ROLE_EXCEPTION_INVALID_PHP_VERSION
            );
        }
        
        $this->assertNull(
            WooUserRolePricesUtils::doCheckPhpVersion(
                PRICE_BY_ROLE_MIN_PHP_VERSION
            )
        );
        
    }
}