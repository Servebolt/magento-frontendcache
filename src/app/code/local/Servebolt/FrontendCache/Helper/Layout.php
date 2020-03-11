<?php
/**
 * @package     Servebolt_FrontendCache
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_Layout
 * 
 * Request helper
*/
class Servebolt_FrontendCache_Helper_Layout extends Servebolt_FrontendCache_Helper_Abstract
{
    /**
     * Mark layout as cacheable or not
     */
    public function markLayout()
    {
        $this->isHandleCacheable() ? : $this->getRequestHelper()->markBypassCache();
    }
        
    /**
     * @param Mage_Core_Controller_Request_Http $request
     *
     * @return bool
     */
    protected function isHandleCacheable()
    {
        $update = $this->getLayoutUpdate();

        if ($update) {
            $updateHandles = $update->getHandles();

            if (!empty($updateHandles)) {
                $disallowedHandles = $this->getConfigHelper()->getDisallowedHandles();

                if (!empty($disallowedHandles)) {
                    $disallowedHandlesInLayout = array_intersect($updateHandles, $disallowedHandles);

                    return empty($disallowedHandlesInLayout);
                }
            }
        }

        return true;
    }
    
    /**
     * @return Mage_Core_Model_Layout_Update
     */
    protected function getLayoutUpdate() 
    {
        /** @var Mage_Core_Model_Layout_Update $update */
        return $this->getLayout()->getUpdate();
    }

    /**
     * Retrieve layout model object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        $layout = parent::getLayout();
        
        return $layout ? $layout : $this->setLayout(Mage::app()->getLayout())->getLayout();
    }
}
