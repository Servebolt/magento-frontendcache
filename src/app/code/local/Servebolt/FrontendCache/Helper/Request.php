<?php
/**
 * @package     Servebolt_FrontendCache
 * @copyright   Copyright (c) 2016 Raske Sider AS
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Helper_Request
 * 
 * Request helper
*/
class Servebolt_FrontendCache_Helper_Request extends Servebolt_FrontendCache_Helper_Abstract
{
    use Servebolt_FrontendCache_Helper_TraitRequest;
    
    const REGISTER_KEY_CACHEABLE                = 'servebolt_frontendcache_cacheable';
    const REGISTER_KEY_NOT_CACHEABLE            = 'servebolt_frontendcache_not_cacheable';
    const REGISTER_KEY_BYPASS                   = 'servebolt_frontendcache_bypass';
    const REGISTER_KEY_DELETE_NO_CACHE_COOKIE   = 'servebolt_frontendcache_delete_no_cache';
    const REGISTER_KEY_HAS_MESSAGES             = 'servebolt_frontendcache_has_messages';
    
    const HEADER_NAME_CACHE_CONTROL = 'Cache-Control';
    const HEADER_NAME_PRAGMA        = 'Pragma';
    const HEADER_NAME_EXPIRE        = 'Expires';
    const HEADER_NAME_SET_COOKIE    = 'Set-Cookie';

    const TEMPORARY_NO_CACHE_COOKIE_LIFETIME = 5;

    /**
     * Marks request as cacheable
     */
    public function markCacheable()
    {
        Mage::register($this::REGISTER_KEY_CACHEABLE, 1, true);
    }

    /**
     * Marks request as not cacheable
     */
    public function markNotCacheable()
    {
        Mage::unregister($this::REGISTER_KEY_CACHEABLE);
        Mage::register($this::REGISTER_KEY_NOT_CACHEABLE, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedCacheable()
    {
        return (bool) Mage::registry($this::REGISTER_KEY_CACHEABLE) && !$this->isMarkedNotCacheable();
    }

    /**
     * @return bool
     */
    public function isMarkedNotCacheable()
    {
        return (bool) Mage::registry($this::REGISTER_KEY_NOT_CACHEABLE);
    }

    /**
     * Marks request to delete no-cache cookie
     */
    public function markUnsetNoCacheCookie()
    {
        Mage::register($this::REGISTER_KEY_DELETE_NO_CACHE_COOKIE, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedUnsetNoCacheCookie()
    {
        return (bool) Mage::registry($this::REGISTER_KEY_DELETE_NO_CACHE_COOKIE);
    }
    
    /**
     * Marks request as cacheable
     */
    public function markBypassCache()
    {
        Mage::register($this::REGISTER_KEY_BYPASS, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedBypassCache()
    {
        return (bool) Mage::registry($this::REGISTER_KEY_BYPASS);
    }

    /**
     * Marks response as having messages to be outputted
     */
    public function markHasMessages()
    {
        Mage::register($this::REGISTER_KEY_HAS_MESSAGES, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedHasMessages()
    {
        return (bool) Mage::registry($this::REGISTER_KEY_HAS_MESSAGES);
    }

    /**
     * Disable caching on external storage by setting special cookie
     *
     * @return void
     */
    public function setNoCacheCookie($noCacheCookieLifetime = null)
    {
        $noCacheCookieName  = $this->getConfigHelper()->getNoCacheCookieName();
        $noCacheCookie       = $this->getCookieSingleton()->get($noCacheCookieName);
        
        if (is_null($noCacheCookieLifetime)) {
            $noCacheCookieLifetime = $this->getConfigHelper()->getNoCacheCookieLifetime();
        }

        if ($noCacheCookie) {
            $this->getCookieSingleton()->renew($noCacheCookieName, $noCacheCookieLifetime);
        } else {
            $this->getCookieSingleton()->set($noCacheCookieName, 1, $noCacheCookieLifetime);
        }
    }

    /**
     * Disable temporarly caching on external storage by setting special cookie
     *
     * @return void
     */
    public function setTemporaryNoCacheCookie()
    {
        $this->setNoCacheCookie($this::TEMPORARY_NO_CACHE_COOKIE_LIFETIME);
    }
    
    /**
     * Set caching validity for external storage 
     *
     * @return void
     */
    public function setExpiresHeader()
    {
        $expiresHeaderName     = $this->getConfigHelper()->getExpiresHeaderName();
        $expiresHeaderLifetime = $this->getConfigHelper()->getExpiresHeaderLifetime();

        $this->setHeader($expiresHeaderName, gmdate('D, d M Y H:i:s \G\M\T', time() + $expiresHeaderLifetime));
    }

    /**
     * Remove no-cache cookie
     *
     * @return void
     */
    public function unsetNoCacheCookie()
    {
        $this->getCookieSingleton()->delete($this->getConfigHelper()->getNoCacheCookieName());
    }

    /**
     * Remove all cookies
     *
     * @return void
     */
    public function unsetAllCookies()
    {        
        $this->unsetHeader($this::HEADER_NAME_SET_COOKIE);
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function markRequest(Mage_Core_Controller_Request_Http $request)
    {
        $this->isCacheable($request) ? $this->markCacheable() : $this->markNotCacheable();
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function addRandomUrlParameter(Mage_Core_Controller_Request_Http $request)
    {
        $this->isCacheable($request) ? $this->markCacheable() : $this->markNotCacheable();
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function processCache()
    {
        $this->getDebugHelper()->logDebugInfo();
        
        if ($this->isMarkedBypassCache()) {
            $this->setNoCacheCookie();
        }
        elseif ($this->isMarkedHasMessages()) {
            $this->setTemporaryNoCacheCookie();
        }
        elseif ($this->isMarkedCacheable()) {
            $this->setExpiresHeader();
            $this->unsetCacheControlHeader();
            $this->unsetPragmaHeader();
            $this->unsetAllCookies();
        }
        
        if (!$this->isMarkedHasMessages() && $this->isMarkedUnsetNoCacheCookie()) {
            $this->unsetNoCacheCookie();
        }
        
        $this->getDebugHelper()->logDebugInfo();
    }
    
    /**
     * @param Mage_Core_Controller_Request_Http $request
     *
     * @return bool
     */
    protected function isCacheable(Mage_Core_Controller_Request_Http $request)
    {
        if ($request->isPost()) {

            return false;
        }

        return $this->isRequestHandleCacheable($request) && $this->isRequestUriCacheable($request);
    }

    /**
     * @return Mage_Core_Model_Cookie
     */
    protected function getCookieSingleton()
    {
        return Mage::getSingleton('core/cookie');
    }

    /**
     * Removes Cache-Control header
     */
    protected function unsetCacheControlHeader()
    {
        $this->unsetHeader($this::HEADER_NAME_CACHE_CONTROL);
    }

    /**
     * Removes Expires header
     */
    protected function unsetExpiresHeader()
    {
        $this->unsetHeader($this::HEADER_NAME_EXPIRE);
    }

    /**
     * Removes Pragma header
     */
    protected function unsetPragmaHeader()
    {
        $this->unsetHeader($this::HEADER_NAME_PRAGMA);
    }

    /**
     * @param $headerName
     */
    protected function unsetHeader($headerName)
    {
        header_remove($headerName);
    }

    /**
     * @param $headerName
     */
    protected function setHeader($headerName, $headerValue)
    {
        header($headerName . ': ' . $headerValue, true);
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     *
     * @return bool
     */
    protected function isRequestHandleCacheable(Mage_Core_Controller_Request_Http $request)
    {
        $allowedRequests = $this->getConfigHelper()->getCacheableRequests();

        if (!$allowedRequests) {

            return false;
        }

        $requestString  = $this->getRequestHandle($request);
        $allowedHandles = $this->getAllowedHandles($allowedRequests);

        foreach ($allowedHandles as $allowedHandle) {
            if (preg_match('/(' . $allowedHandle . ')/mi', $requestString)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     *
     * @return bool
     */
    protected function isRequestUriCacheable(Mage_Core_Controller_Request_Http $request)
    {
        $nonCacheableUris = $this->getConfigHelper()->getNonCacheableUris();

        if (!$nonCacheableUris) {

            return true;
        }

        $requestUri = $request->getRequestUri();
        
        if (in_array($requestUri, $nonCacheableUris)) {
            
            return false;
        }

        return true;
    }
}
