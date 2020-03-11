<?php
/**
 * @package     Servebolt_FrontendCache
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_Formkey
 * 
 * Debug helper
 */
class Servebolt_FrontendCache_Helper_Formkey extends Servebolt_FrontendCache_Helper_Abstract
{
    const URL_PARAM_FORM_KEY = 'form_key';
    use Servebolt_FrontendCache_Helper_TraitRequest;
    
    /**
     * Check whether debugging mode is enabled
     *
     * @return bool
     */
    public function isFormkeyValidationEnabled()
    {
        return $this->getConfigHelper()->isFormkeyValidationEnabled();
    }
    
    /**
     * Logs response information to file
     */
    public function fakeFormkey(Mage_Core_Controller_Request_Http $request) 
    {
        if ($this->isFormkeyValidationEnabled()) {
            $bypassRequests = $this->getConfigHelper()->getFormkeyBypassRequests();

            if (!$bypassRequests) {

                return false;
            }

            $requestString  = $this->getRequestHandle($request);
            $bypassHandles = $this->getAllowedHandles($bypassRequests);

            foreach ($bypassHandles as $bypassHandle) {
                if (preg_match('/(' . $bypassHandle . ')/mi', $requestString)) {
                    $sessionFormKey = Mage::getSingleton('core/session')->getFormKey();
                    $urlFormKey     = $request->getParam($this::URL_PARAM_FORM_KEY, null); 
                    
                    if ($sessionFormKey && ($urlFormKey != $sessionFormKey)) {
                        $request->setParam($this::URL_PARAM_FORM_KEY, $sessionFormKey);

                        return true;
                    }
                }
            }           
        }

        return false;
    }

}
