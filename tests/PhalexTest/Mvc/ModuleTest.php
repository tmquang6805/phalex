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

        $expectedRegisteredModules = require './tests/config/module_register.result.php';
        $expectedModulesConfig     = require './tests/config/module_config.result.php';
        $expectedModulesAutoload   = require './tests/config/autoload.result.php';
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
