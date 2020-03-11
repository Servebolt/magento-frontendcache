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
 * Class Servebolt_FrontendCache_Test_Config_Config
 */
class Servebolt_FrontendCache_Test_Config_Config extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * Test global config
     * 
     * @test
     * @loadExpectation
     */
    public function globalConfig()
    {
        self::assertModuleVersion($this->expected('module')->getVersion());
        self::assertModuleCodePool($this->expected('module')->getCodePool());
    }

    /**
     * Test helpers
     * 
     * @test
     * @loadExpectation
     */
    public function helpersConfig()
    {
        $helpers = $this->expected('helpers')->getData();
        foreach ($helpers as $className => $helper) {
            $this->assertHelperAlias($helper, $className);

            $classExists = class_exists($className);
            $this->assertTrue($classExists, 'Class ' . $className . ' is not defined');

            if ($classExists) {
                $this->assertInstanceOf($className, Mage::helper($helper));
            }
        }
    }

    /**
     * Test models and its resources
     */
    protected function modelsConfig()
    {
        $models = $this->expected('models')->getData();

        foreach ($models as $className => $model) {
            $this->assertModelAlias($model, $className);

            $classExists = class_exists($className);
            $this->assertTrue($classExists, 'Class ' . $className . ' is not defined');

            if ($classExists) {
                $this->assertInstanceOf($className, Mage::getModel($model));
            }
        }
    }

    /**
     * Test observers
     */
    protected function observersConfig()
    {
        $observers = $this->expected('observers')->getData();
        foreach ($observers as $area => $eventData) {
            foreach ($eventData as $eventName => $observerData) {
                foreach ($observerData as $className => $methodName) {
                    $this->assertEventObserverDefined($area, $eventName, $className, $methodName);
                }
            }
        }
    }
}
