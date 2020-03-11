<?php
/**
 * @package     Servebolt_FrontendCache_Model_Observer
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Model_Observer
 */
class Servebolt_FrontendCache_Model_Observer
{
    use Servebolt_FrontendCache_Helper_TraitHelper;
    
    /** @var Servebolt_FrontendCache_Helper_Config */
    protected $cacheHelper;

    /** @var Servebolt_FrontendCache_Helper_Request */
    protected $requestHelper;

    /** @var Servebolt_FrontendCache_Helper_Layout */
    protected $layoutHelper;

    /**
     * Check if cache cookies are enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->getConfigHelper()->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @event controller_action_predispatch
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            
            return $this;
        }
        
        /** @var Mage_Core_Controller_Varien_Action $action */
        $action = $observer->getEvent()->getControllerAction();
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $action->getRequest();
        
        $this->getRequestHelper()->markRequest($request);
        $this->getFormkeyHelper()->fakeFormkey($request);
        $this->getCheckoutHelper()->controlCheckout();

        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @event controller_action_postdispatch
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function processLogout(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            
            return $this;
        }
        
        $this->getRequestHelper()->markUnsetNoCacheCookie();

        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @event controller_action_postdispatch
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function processPostDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            
            return $this;
        }
        
        $this->getLayoutHelper()->markLayout();
        $this->getCheckoutHelper()->markCheckout();
        
        $this->getRequestHelper()->processCache();

        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @event core_session_abstract_clear_messages
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function processClearSessionMessages(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            
            return $this;
        }

        /** @var Varien_Event $event */
        $event = $observer->getEvent();
        
        if ($event->getCount()) {
            $this->getRequestHelper()->markHasMessages();
        }

        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @event core_session_abstract_clear_messages
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function processAddSessionMessage(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            
            return $this;
        }
            
        $this->getRequestHelper()->markHasMessages();

        return $this;
    }
}
