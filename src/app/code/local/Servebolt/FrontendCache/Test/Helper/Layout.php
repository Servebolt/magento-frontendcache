<?php
/**
 * Copyright © 2016- Raske Sider AS <post@raskesider.no>. All rights reserved.
 *
 * See LICENSE.md for license details.
 *
 * @copyright   2016- Raske Sider AS <post@raskesider.no>
 * @author      Miply <magento@miply.no>
 */

/**
 * Class Servebolt_FrontendCache_Test_Helper_Layout
 */
class Servebolt_FrontendCache_Test_Helper_Layout extends EcomDev_PHPUnit_Test_Case_Controller
{
    use Servebolt_FrontendCache_Helper_TraitHelper;

    /**
     * @test
     *
     * @loadFixture ~Servebolt_FrontendCache/config
     * @loadFixture ~Servebolt_FrontendCache/catalog
     *              
     * @registry current_category
     * @registry servebolt_frontendcache_bypass
     * @registry servebolt_frontendcache_cacheable
     * @registry current_entity_key
     *           
     * @singleton servebolt_frontendcache/request
     * @singleton servebolt_frontendcache/observer
     * @singleton checkout/session
     */
    public function markLayoutPageCacheable()
    {
        $disallowedLayoutHandle = 'customer_logged_in';

        $helperConfig = [
            'unsetCacheControlHeader' => [
                'frequency' => 'once',
                'return' => null,
            ],
            'unsetPragmaHeader' => [
                'frequency' => 'once',
                'return' => null,
            ],
            'setExpiresHeader' => [
                'frequency' => 'once',
                'return' => null,
            ],
            'unsetAllCookies' => [
                'frequency' => 'once',
                'return' => null,
            ],
        ];
        $this->_mockRequestHelper($helperConfig);

        $this->dispatch('catalog/category/view/id/3');
        
        $this->assertLayoutHandleNotLoaded($disallowedLayoutHandle);
    }

    /**
     * @test
     * 
     * @loadFixture ~Servebolt_FrontendCache/config
     * @loadFixture ~Servebolt_FrontendCache/catalog
     * @loadFixture ~Servebolt_FrontendCache/customer
     *              
     * @registry current_customer
     * @registry current_category
     * @registry servebolt_frontendcache_bypass
     * @registry servebolt_frontendcache_cacheable
     * @registry current_entity_key
     *           
     * @singleton customer/session
     * @singleton servebolt_frontendcache/request
     * @singleton servebolt_frontendcache/observer
     * @singleton checkout/session
     */
    public function markLayoutBypassCache()
    {
        $disallowedLayoutHandle = 'customer_logged_in';

        $helperConfig = [
            'setNoCacheCookie' => [
                'frequency' => 'once',
                'return' => null,
            ],
        ];
        $this->_mockRequestHelper($helperConfig);
        
        $this->loginCustomerById(1);

        $this->dispatch('catalog/category/view/id/3');
        
        $this->assertLayoutHandleLoaded($disallowedLayoutHandle);
    }
    
    
    /**
     * @param array $incrementIds
     *
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function _mockRequestHelper(array $mockMethods)
    {
        $helperAlias = 'servebolt_frontendcache/request';
        
        $helperMock  = $this->getHelperMock(
            $helperAlias,
            array_keys($mockMethods)
        );

        foreach ($mockMethods as $methodName => $methodConfig) {
            $helperMock
                ->expects($this->{$methodConfig['frequency']}())
                ->method($methodName)
                ->will($this->returnValue($methodConfig['return']));
        }

        $this->replaceByMock('helper', $helperAlias, $helperMock);

        return $helperMock;
    }

    /**
     * @param $customerId
     */
    protected function loginCustomerById($customerId)
    {
        /* Create customer session mock, for making our session singleton isolated */
        $customerSessionAlias = 'customer/session';
        
        $customerSessionMock  = $this->getModelMock($customerSessionAlias, array('renewSession'));
        $this->replaceByMock('singleton', $customerSessionAlias, $customerSessionMock);

        $customerSessionMock->loginById($customerId);
    }
}
