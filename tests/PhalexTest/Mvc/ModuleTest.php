<?php

namespace PhalexTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Mvc\Module;
use Phalex\Mvc\Module\Cache\CacheInterface;

/**
 * Description of ModuleTest
 *
 * @author quangtm
 */
class ModuleTest extends TestCase
{
    /**
     * @expectedException Phalex\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid parameters for init phalcon extesion
     */
    public function testConstructRaiseExptionEmptyParameters()
    {
        new Module([], []);
    }

    /**
     * @expectedException Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage Not found module "Application"
     */
    public function testConstructRaiseExceptionNotFoundClass()
    {
        $moduleName = 'Application';
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
    
    public function testConstructSuccessWithCacheNotData()
    {
        $expectedRegisteredModules = require './tests/config/module_register.result.php';
        $expectedModulesAutoload   = require './tests/config/autoload.result.php';
        
        $cacheMock = $this->getMock(CacheInterface::class);
        $cacheMock->expects($this->once())
                ->method('getRegisteredModules')
                ->will($this->returnValue(false));
        
        $cacheMock->expects($this->once())
                ->method('setRegisteredModules');
        
        $cacheMock->expects($this->once())
                ->method('getAutoloadModulesConfig')
                ->will($this->returnValue(false));
        
        $cacheMock->expects($this->once())
                ->method('setAutoloadModulesConfig');

        $moduleNames = ['Application', 'Backend'];

        $module = new Module($moduleNames, ['./tests/module'], $cacheMock);
        foreach ($moduleNames as $moduleName) {
            $this->assertTrue(class_exists("$moduleName\\Module"));
        }
        
        $this->assertEquals($expectedRegisteredModules, $module->getRegisteredModules());
        $this->assertEquals($expectedModulesAutoload, $module->getModulesAutoloadConfig());
    }

    public function testConstructSuccessWithCacheHasData()
    {
        $expectedRegisteredModules = require './tests/config/module_register.result.php';
        $expectedModulesAutoload   = require './tests/config/autoload.result.php';
        
        $cacheMock = $this->getMock(CacheInterface::class);
        $cacheMock->expects($this->once())
                ->method('getRegisteredModules')
                ->will($this->returnValue($expectedRegisteredModules));
        
        $cacheMock->expects($this->never())
                ->method('setRegisteredModules');
        
        $cacheMock->expects($this->once())
                ->method('getAutoloadModulesConfig')
                ->will($this->returnValue($expectedModulesAutoload));
        
        $cacheMock->expects($this->never())
                ->method('setAutoloadModulesConfig');

        $moduleNames = ['Application', 'Backend'];

        $module = new Module($moduleNames, ['./tests/module'], $cacheMock);
        foreach ($moduleNames as $moduleName) {
            $this->assertTrue(class_exists("$moduleName\\Module"));
        }


        $this->assertEquals($expectedRegisteredModules, $module->getRegisteredModules());
        $this->assertEquals($expectedModulesAutoload, $module->getModulesAutoloadConfig());
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage Class "WrongModule\Module" must be extended from Phalex\Mvc\Module\AbstractModule
     */
    public function testGetModulesConfigRaiseException()
    {
        $moduleNames = ['Application', 'WrongModule'];
        $moduleMock  = new Module($moduleNames, ['./tests/module']);
        $moduleMock->getModulesConfig();
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage Class "WrongModule\Module" must be extended from Phalex\Mvc\Module\AbstractModule
     */
    public function testGetModulesAutoloadConfigRaiseException()
    {
        $moduleNames = ['Application', 'WrongModule'];
        $moduleMock  = new Module($moduleNames, ['./tests/module']);
        $moduleMock->getModulesAutoloadConfig();
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage The autoloader configuration for module "WrongModuleConfig" is invalid
     */
    public function testGetModulesAutoloadConfigWrongDataType()
    {
        $moduleNames = ['Application', 'WrongModuleConfig'];
        $moduleMock  = new Module($moduleNames, ['./tests/module']);
        $moduleMock->getModulesAutoloadConfig();
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage The configuration for module "WrongModuleConfig" is invalid
     */
    public function testGetModulesConfigWrongDataType()
    {
        $moduleNames = ['Application', 'WrongModuleConfig'];
        $moduleMock  = new Module($moduleNames, ['./tests/module']);
        $moduleMock->getModulesConfig();
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage The view path for module "WrongModuleViewPath" is invalid
     */
    public function testGetModulesConfigWrongViewPath()
    {
        $moduleNames = ['Application', 'WrongModuleViewPath'];
        $moduleMock  = new Module($moduleNames, ['./tests/module']);
        $moduleMock->getModulesConfig();
    }
}
