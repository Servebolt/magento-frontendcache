<?php
/**
 * @package     Servebolt_FrontendCache
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Model_Core_Message_Collection
 */
class Servebolt_FrontendCache_Model_Core_Message_Collection extends Mage_Core_Model_Message_Collection
{
   const EVENT_PREFIX = 'servebolt_frontendcache_message_collection_';

    /**
     * {@inheritDoc}
     */
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        Mage::dispatchEvent($this::EVENT_PREFIX . 'add_message_before', array('message' => $message));
        
        return parent::addMessage($message);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        Mage::dispatchEvent(
            $this::EVENT_PREFIX . 'clear_before', 
            array('messages' => $this->_messages, 'count' => $this->count())
        );

        return parent::clear();
    }

    
}
