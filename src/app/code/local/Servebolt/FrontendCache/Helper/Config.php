<?php
/**
 * @package     Servebolt_FrontendCache
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_Config
 * 
 * Advanced cache helper
 */
class Servebolt_FrontendCache_Helper_Config extends Mage_Core_Helper_Abstract
{
    /** Paths to external cache config options */
    const XPATH_MODULE         = 'servebolt_frontendcache';
    const XPATH_MODULE_ENABLED = 'enabled';
    
    /** Cookies settings */
    const XPATH_CACHE_COOKIES            = 'cookies';
    const XPATH_CACHE_CACHEABLE_REQUESTS = 'allowed_requests';
    const XPATH_CACHE_NON_CACHEABLE_URIS = 'non_cacheable_uris';
    const XPATH_CACHE_DISALLOWED_HANDLES = 'disallowed_handles';
    
    /** Headers settings */
    const XPATH_CACHE_HEADERS            = 'headers';
    const XPATH_CACHE_HEADERS_EXPIRES    = 'expires';
    
    const XPATH_LIFETIME = 'lifetime';
    
    /** Debugging settings */
    const XPATH_DEBUGGING         = 'debugging';
    const XPATH_DEBUGGING_ENABLED = 'enabled';
    const XPATH_DEBUGGING_COOKIES = 'cookies';
    
    /** Formkey bypassing settings */
    const XPATH_FORMKEY_BYPASS          = 'formkey_bypass';
    const XPATH_FORMKEY_BYPASS_ENABLED  = 'enabled';
    const XPATH_FORMKEY_BYPASS_REQUESTS = 'bypass_requests';

    /** Cookie name for disabling external caching */
    const NO_CACHE_COOKIE = 'no_cache';
    /** Header name for cache validity */
    const EXPIRES_HEADER = 'Expires';
    
    /** Request URI settings */
    const REQUEST_URI_HOMEPAGE = 'servebolt_frontendcache_hompage_uri';

    protected $enabled;
    protected $debuggingEnabled;
    protected $formkeyEnabled;

    /**
     * Check whether external cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (is_null($this->enabled)) {
            $this->enabled = $this->getConfigFlag($this::XPATH_MODULE_ENABLED);
        }
        
        return $this->enabled;
    }

    /**
     * Check whether debugging mode is enabled
     *
     * @return bool
     */
    public function isDebuggingEnabled()
    {
        if (is_null($this->debuggingEnabled)) {
            $this->debuggingEnabled = (bool) trim($this->getDebuggingConfig($this::XPATH_DEBUGGING_ENABLED));
        }
        
        return $this->debuggingEnabled;
    }

    /**
     * Check whether formkey bypassing mode is enabled
     *
     * @return bool
     */
    public function isFormkeyValidationEnabled()
    {
        if (is_null($this->formkeyEnabled)) {
            $this->formkeyEnabled = (bool) trim($this->getFormkeyConfig($this::XPATH_FORMKEY_BYPASS_ENABLED));
        }
        
        return $this->formkeyEnabled;
    }

    /**
     * Return debugging cookies
     *
     * @return array
     */
    public function getDebuggingCookies()
    {
        return trim($this->getDebuggingConfig($this::XPATH_DEBUGGING_COOKIES));
    }

    /**
     * Returns a lifetime of no-cache cookies
     *
     * @return string Seconds
     */
    public function getNoCacheCookieLifetime()
    {
        $configuredCookie = $this->getCookieConfig($this->getNoCacheCookieName(), $this::XPATH_LIFETIME);

        return $configuredCookie ?: Mage::getModel('core/cookie')->getLifetime();
    }

    /**
     * Returns a lifetime of Expires header
     *
     * @return string Seconds
     */
    public function getExpiresHeaderLifetime()
    {
        $configuredHeader = $this->getHeaderConfig($this::XPATH_CACHE_HEADERS_EXPIRES, $this::XPATH_LIFETIME);

        return $configuredHeader ?: Mage::getModel('core/cookie')->getLifetime();
    }

    /**
     * Returns tree of cacheable requests
     *
     * -> module
     * ---> controller1
     * ---> controller2
     * -----> action1
     * -----> action2
     *
     * @return string[]
     */
    public function getCacheableRequests()
    {
        return $this->getConfig($this::XPATH_CACHE_CACHEABLE_REQUESTS);
    }

    /**
     * @return string[]
     */
    public function getNonCacheableUris()
    {
        $uris = $this->getConfig($this::XPATH_CACHE_NON_CACHEABLE_URIS);
        
        /** Replace special homepage URI identifier with real URI '/' (slash) */
        if (is_array($uris) && (array_key_exists($this::REQUEST_URI_HOMEPAGE, $uris))) {
            $uris['/'] = $uris[$this::REQUEST_URI_HOMEPAGE];
            unset($uris[$this::REQUEST_URI_HOMEPAGE]);
        }

        return is_array($uris) ? array_keys(array_filter($uris, function($value) { return $value; })) : [];
    }

    /**
     * @return string[]
     */
    public function getDisallowedHandles()
    {
        $handles = $this->getConfig($this::XPATH_CACHE_DISALLOWED_HANDLES);
        
        return is_array($handles) ? array_keys(array_filter($handles, function($value) { return $value; })) : [];
    }

    /**
     * @return string
     */
    public function getNoCacheCookieName()
    {
        return $this::NO_CACHE_COOKIE;
    }

    /**
     * @return string
     */
    public function getExpiresHeaderName()
    {
        return $this::EXPIRES_HEADER;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    protected function getConfigFlag($path)
    {
        return Mage::getStoreConfigFlag($this::XPATH_MODULE . '/' . $path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    protected function getConfig($path)
    {
        return Mage::getStoreConfig($this::XPATH_MODULE . '/' . $path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    protected function getDebuggingConfig($path)
    {
        return $this->getConfig($this::XPATH_DEBUGGING . '/' . $path);
    }

    /**
     * @param $cookieName
     * @param $configType
     *
     * @return mixed
     */
    protected function getCookieConfig($cookieName, $configType)
    {
        return $this->getConfig($this::XPATH_CACHE_COOKIES . '/' . $cookieName . '/' . $configType);
    }

    /**
     * @param $headerName
     * @param $configType
     *
     * @return mixed
     */
    protected function getHeaderConfig($headerName, $configType)
    {
        return $this->getConfig($this::XPATH_CACHE_HEADERS . '/' . $headerName . '/' . $configType);
    }

    /**
     * Check whether formkey bypassing mode is enabled
     *
     * @param $path
     *
     * @return array
     */
    public function getFormkeyConfig($path)
    {
        return $this->getConfig($this::XPATH_FORMKEY_BYPASS . '/' . $path);
    }

    /**
     * Returns tree of cacheable for which bypassing form key should be applied
     *
     * -> module
     * ---> controller1
     * ---> controller2
     * -----> action1
     * -----> action2
     *
     * @return string[]
     */
    public function getFormkeyBypassRequests()
    {
        return $this->getFormkeyConfig($this::XPATH_FORMKEY_BYPASS_REQUESTS);
    }
}
