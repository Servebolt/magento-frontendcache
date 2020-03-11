<?php
/**
 * @package     Servebolt_FrontendCache_Model_Observer
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_TraitHelper
 */
trait Servebolt_FrontendCache_Helper_TraitHelper
{
    /** @var Servebolt_FrontendCache_Helper_Config */
    protected $configHelper;

    /** @var Servebolt_FrontendCache_Helper_Request */
    protected $requestHelper;

    /** @var Servebolt_FrontendCache_Helper_Layout */
    protected $layoutHelper;
 
    /** @var Servebolt_FrontendCache_Helper_Debug */
    protected $debuggingHelper;
 
    /** @var Servebolt_FrontendCache_Helper_Checkout */
    protected $checkoutHelper;
    
    /** @var Servebolt_FrontendCache_Helper_Formkey */
    protected $formkeyHelper;



    /**
     * @return Servebolt_FrontendCache_Helper_Config
     */
    protected function getConfigHelper()
    {
        if (!$this->configHelper) {
            $this->configHelper = Mage::helper('servebolt_frontendcache/config');
        }

        return $this->configHelper;
    }

    /**
     * @return Servebolt_FrontendCache_Helper_Request
     */
    protected function getRequestHelper()
    {
        if (!$this->requestHelper) {
            $this->requestHelper = Mage::helper('servebolt_frontendcache/request');
        }

        return $this->requestHelper;
    }

    /**
     * @return Servebolt_FrontendCache_Helper_Layout
     */
    protected function getLayoutHelper()
    {
        if (!$this->layoutHelper) {
            $this->layoutHelper = Mage::helper('servebolt_frontendcache/layout');
        }

        return $this->layoutHelper;
    }

    /**
     * @return Servebolt_FrontendCache_Helper_Debug
     */
    protected function getDebugHelper()
    {
        if (!$this->debuggingHelper) {
            $this->debuggingHelper = Mage::helper('servebolt_frontendcache/debug');
        }

        return $this->debuggingHelper;
    }

    /**
     * @return Servebolt_FrontendCache_Helper_Checkout
     */
    protected function getCheckoutHelper()
    {
        if (!$this->checkoutHelper) {
            $this->checkoutHelper = Mage::helper('servebolt_frontendcache/checkout');
        }

        return $this->checkoutHelper;
    }

    /**
     * @return Servebolt_FrontendCache_Helper_Formkey
     */
    protected function getFormkeyHelper()
    {
        if (!$this->formkeyHelper) {
            $this->formkeyHelper = Mage::helper('servebolt_frontendcache/formkey');
        }

        return $this->formkeyHelper;
    }
}
