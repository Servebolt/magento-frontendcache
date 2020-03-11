<?php
/**
 * @package     Servebolt_FrontendCache
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_Checkout
 * 
 * Request helper
*/
class Servebolt_FrontendCache_Helper_Checkout extends Servebolt_FrontendCache_Helper_Abstract
{
    protected $hadItems;
    
    /**
     * Mark checkout data as cacheable or not
     */
    public function markCheckout()
    {
        if ($this->hasItems()) {
            $this->getRequestHelper()->markBypassCache();
        }
        elseif ($this->hadItems()) {
            $this->getRequestHelper()->markUnsetNoCacheCookie();
        }
    }

    /**
     * Stores info about cart for later processing
     */
    public function controlCheckout()
    {
        $this->hadItems = $this->hasItems();
    }

    /**
     * Checks if quote is cacheable
     *
     * Quote is cacheable if it has no items
     *
     * @return bool
     */
    protected function isCacheable()
    {
        return !$this->hasItems();
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * @return bool
     */
    protected function hasItems()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getCheckoutSession()->getQuote();

        foreach ($quote->getAllItems() as $item) {

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function hadItems()
    {
        return (bool) $this->hadItems;
    }
}
