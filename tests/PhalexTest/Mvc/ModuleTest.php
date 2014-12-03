<?php

namespace PhalexTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Mvc\Module;
use Phalex\Mvc\Exception\RuntimeException;
use Phalex\Mvc\Exception\InvalidArgumentException;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of ModuleTest
 *
 * @author quangtm
 */
class ModuleTest extends TestCase
{
    public function testConstructRaiseExptionEmptyParameters()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Invalid parameters for init phalcon extesion');
        new Module([], []);
    }

    public function testConstructRaiseExceptionNotFoundClass()
    {
        $moduleName = 'Application';
        $this->setExpectedException(RuntimeException::class, sprintf('Cannot autoload module "%s"', $moduleName));
        new Module([$moduleName], ['./test']);
    }

    public function testConstructSuccessWithoutCache()
    {
        $moduleNames = ['Application', 'Backend'];

        $module = new Module($moduleNames, ['./tests/module']);
        foreach ($moduleNames as $moduleName) {
            $this->assertTrue(class_exists("$moduleName\\Module"));
        }

        $expectedRegisteredModules = [
            'Application' => [
                'className' => 'Application\\Module',
                'path'      => './tests/module/Application/Module.php'
            ],
            'Backend'     => [
                'className' => 'Backend\\Module',
                'path'      => './tests/module/Backend/Module.php'
            ],
        ];

        $configModuleApp = require './tests/module/Application/config/module.config.php';
        $configModuleBe = require './tests/module/Backend/config/module.config.php';
        $expectedModulesConfig = ArrayUtils::merge($configModuleApp, $configModuleBe);
        foreach ($expectedModulesConfig as $moduleName => $moduleConfig) {
            if (isset($expectedModulesConfig[$moduleName]['view'])) {
                $expectedModulesConfig[$moduleName]['view'] = realpath($moduleConfig['view']);
            }
        }

        $autoloadModuleApp = require './tests/module/Application/config/autoload.config.php';
        $autoloadModuleBe = require './tests/module/Backend/config/autoload.config.php';
        $expectedModulesAutoload = ArrayUtils::merge($autoloadModuleApp, $autoloadModuleBe);
        foreach ($expectedModulesAutoload as $moduleName => $configAutoload) {
            foreach ($configAutoload as $key => $value) {
                $expectedModulesAutoload[$moduleName][$key] = realpath($value);
            }
        }

        $this->assertEquals($expectedRegisteredModules, $module->getRegisteredModules());
        $this->assertEquals($expectedModulesConfig, $module->getModulesConfig());
        $this->assertEquals($expectedModulesAutoload, $module->getModulesAutoloadConfig());
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage The autoloader configuration for module "Application" is invalid
     */
    public function testGetModulesAutoloadConfigRaiseException()
    {
        $moduleNames = ['Application', 'Backend'];
        $moduleMock  = new ModuleMock($moduleNames, ['./tests/module']);
        $moduleMock->getModulesAutoloadConfig();
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage The view path for module "Application" is invalid
     */
    public function testGetModulesConfigRaiseException()
    {
        $moduleNames = ['Application', 'Backend'];
        $moduleMock  = new ModuleMock($moduleNames, ['./tests/module']);
        $moduleMock->getModulesConfig();
    }
}
