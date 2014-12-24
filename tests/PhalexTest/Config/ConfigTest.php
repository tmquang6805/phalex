<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Config;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Config\Config;
use Phalex\Config\Cache\CacheInterface;
use Zend\Stdlib\ArrayUtils;
use Phalex\Mvc\Module;
use Mockery as m;

/**
 * Description of ConfigTest
 *
 * @author quangtm
 */
class ConfigTest extends TestCase
{
    public function testGetConfig()
    {
        $expected      = require './tests/config/config.result.php';
        $configApp     = require './tests/module/Application/config/module.config.php';
        $configBe      = require './tests/module/Backend/config/module.config.php';
        $modulesConfig = ArrayUtils::merge($configApp, $configBe);
        
        $moduleHandler = m::mock(Module::class);
        $moduleHandler->shouldReceive('getModulesConfig')->andReturn($modulesConfig);
        
        $globPaths     = [
            './tests/config/{,*.}{global}.php',
            './tests/config/local.php'
        ];

        $resultConfig = (new Config($moduleHandler, $globPaths))
                ->getConfig();
        $this->assertInternalType('array', $resultConfig);
        $this->assertEquals($expected, $resultConfig);

        $resultConfig = (new Config($moduleHandler, './tests/config/{,*.}{global,local}.php'))
                ->getConfig();
        $this->assertInternalType('array', $resultConfig);
        $this->assertEquals($expected, $resultConfig);
    }
    
    public function testGetConfigWithCacheHasData()
    {
        $expected      = require './tests/config/config.result.php';
        $configApp     = require './tests/module/Application/config/module.config.php';
        $configBe      = require './tests/module/Backend/config/module.config.php';
        
        $cacheMock = $this->getMock(CacheInterface::class);
        $cacheMock->expects($this->once())
                ->method('getConfig')
                ->will($this->returnValue($expected));
        
        $cacheMock->expects($this->never())
                ->method('setConfig');
        
        $modulesConfig = ArrayUtils::merge($configApp, $configBe);
        $moduleHandler = m::mock(Module::class);
        $moduleHandler->shouldReceive('getModulesConfig')->andReturn($modulesConfig);
        $globPaths     = [
            './tests/config/{,*.}{global}.php',
            './tests/config/local.php'
        ];

        (new Config($moduleHandler, $globPaths, $cacheMock))
                ->getConfig();
    }
    
    public function testGetConfigWithCacheNotData()
    {
        $configApp     = require './tests/module/Application/config/module.config.php';
        $configBe      = require './tests/module/Backend/config/module.config.php';
        
        $cacheMock = $this->getMock(CacheInterface::class);
        $cacheMock->expects($this->once())
                ->method('getConfig')
                ->will($this->returnValue(false));
        
        $cacheMock->expects($this->once())
                ->method('setConfig');
        
        $modulesConfig = ArrayUtils::merge($configApp, $configBe);
        $moduleHandler = m::mock(Module::class);
        $moduleHandler->shouldReceive('getModulesConfig')->andReturn($modulesConfig);
        $globPaths     = [
            './tests/config/{,*.}{global}.php',
            './tests/config/local.php'
        ];

        (new Config($moduleHandler, $globPaths, $cacheMock))
                ->getConfig();
    }

    /**
     * @expectedExceptionMessage The config in "./tests/config/wrong_config_autoload.php" file must be array data type
     * @expectedException Phalex\Config\Exception\RuntimeException
     */
    public function testGetConfigRaiseExceptionFile()
    {
        $configApp     = require './tests/module/Application/config/module.config.php';
        $configBe      = require './tests/module/Backend/config/module.config.php';
        $modulesConfig = ArrayUtils::merge($configApp, $configBe);
        $moduleHandler = m::mock(Module::class);
        $moduleHandler->shouldReceive('getModulesConfig')->andReturn($modulesConfig);
        (new Config($moduleHandler, './tests/config/wrong_config_autoload.php'))
                ->getConfig();
    }

    /**
     * @expectedExceptionMessage Config view path is not valid
     * @expectedException Phalex\Config\Exception\InvalidArgumentException
     */
    public function testGetConfigRaiseExceptionViewPath()
    {
        $modulesConfig = require './tests/config/wrong_config_autoload_view_path.php';
        $moduleHandler = m::mock(Module::class);
        $moduleHandler->shouldReceive('getModulesConfig')->andReturn($modulesConfig);
        (new Config($moduleHandler, './tests/config/{,*.}{global,local}.php'))
                ->getConfig();
    }
    
    /**
     * @expectedExceptionMessage Config view path for "not_exist" module is invalid
     * @expectedException Phalex\Config\Exception\RuntimeException
     */
    public function testGetConfigRaiseExceptionViewPathNotExisted()
    {
        $modulesConfig = require './tests/config/wrong_config_autoload_view_path_not_exist.php';
        $moduleHandler = m::mock(Module::class);
        $moduleHandler->shouldReceive('getModulesConfig')->andReturn($modulesConfig);
        (new Config($moduleHandler, './tests/config/{,*.}{global,local}.php'))
                ->getConfig();
    }
}
