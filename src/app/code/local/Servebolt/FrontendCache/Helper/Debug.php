<?php
/**
 * @package     Servebolt_FrontendCache
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_Debug
 * 
 * Debug helper
 */
class Servebolt_FrontendCache_Helper_Debug extends Servebolt_FrontendCache_Helper_Abstract
{    
    const LOG_FILE_NAME = 'servebolt_frontendcache.log';
    
    protected $requestId;
    
    /**
     * Check whether debugging mode is enabled
     *
     * @return bool
     */
    public function isDebuggingEnabled()
    {
        return $this->getConfigHelper()->isDebuggingEnabled();
    }
    
    /**
     * Logs response information to file
     */
    public function logDebugInfo() 
    {
        if ($this->isDebuggingEnabled()) {
            $messages = [];
            $messages['ID']         = $this->getRequestId();
            $messages['url']        = Mage::app()->getRequest()->getRequestUri();
            $messages['bypass']     = $this->getRequestHelper()->isMarkedBypassCache();
            $messages['cacheable']  = $this->getRequestHelper()->isMarkedCacheable();            
            $messages['headers']    = $this->getHeaders();
            
            Mage::log(var_export($messages, true), null, self::LOG_FILE_NAME);
        }
    }

    /**
     * Check whether external cache is enabled
     *
     * @return array
     */
    protected function getHeaders()
    {
        return Mage::app()->getResponse()->getHeaders();
    }

    /**
     * @return mixed
     */
    public function getRequestId()
    {
        if (is_null($this->requestId)) {
            $this->requestId = date('H:i:s');
        }
        
        return $this->requestId;
    }
}
