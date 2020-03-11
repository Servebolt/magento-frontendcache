<?php
/**
 * @package     Servebolt_FrontendCache_Model_Observer
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_TraitRequest
 */
trait Servebolt_FrontendCache_Helper_TraitRequest
{
    /**
    * @param Mage_Core_Controller_Request_Http $request
    *
    * @return string
    */
    protected function getRequestHandle(Mage_Core_Controller_Request_Http $request)
    {
        $module        = $request->getModuleName();
        $controller    = $request->getControllerName();
        $action        = $request->getActionName();

        return $module . $this->getHandleSeparator() . $controller . $this->getHandleSeparator() . $action;
    }

    /**
     * @param $allowedRequests
     *
     * @return array
     */
    protected function getAllowedHandles($allowedRequests)
    {
        $allowedHandles = [];

        foreach ($allowedRequests as $allowedModule => $allowedControllers) {
            if (!is_array($allowedControllers)) {
                $allowedHandles[] = $allowedModule
                    . $this->getHandleSeparator()
                    . $this->getHandleAnyChar()
                    . $this->getHandleSeparator()
                    . $this->getHandleAnyChar();

                continue;
            }

            foreach ($allowedControllers as $allowedController => $allowedActions) {
                if (!is_array($allowedActions)) {
                    $allowedHandles[] = $allowedModule
                        . $this->getHandleSeparator()
                        . $allowedController
                        . $this->getHandleSeparator()
                        . $this->getHandleAnyChar();

                    continue;
                }

                foreach ($allowedActions as $allowedAction => $unused) {
                    $allowedHandles[] = $allowedModule
                        . $this->getHandleSeparator()
                        . $allowedController
                        . $this->getHandleSeparator()
                        . $allowedAction;
                }
            }
        }

        return $allowedHandles;
    }

    /**
     * @return string
     */
    private function getHandleSeparator()
    {
        return '_';
    }

    /**
     * @return string
     */
    private function getHandleAnyChar()
    {
        return '.*';
    }
}
