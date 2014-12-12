<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Config;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Config\Config;
use Zend\Stdlib\ArrayUtils;

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
        $globPaths     = [
            './tests/config/{,*.}{global}.php',
            './tests/config/local.php'
        ];

        $resultConfig = (new Config($modulesConfig, $globPaths))
                ->getConfig();
        $this->assertInternalType('array', $resultConfig);
        $this->assertEquals($expected, $resultConfig);

        $resultConfig = (new Config($modulesConfig, './tests/config/{,*.}{global,local}.php'))
                ->getConfig();
        $this->assertInternalType('array', $resultConfig);
        $this->assertEquals($expected, $resultConfig);
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
        (new Config($modulesConfig, './tests/config/wrong_config_autoload.php'))
                ->getConfig();
    }

    /**
     * @expectedExceptionMessage Config view path is not valid
     * @expectedException Phalex\Config\Exception\InvalidArgumentException
     */
    public function testGetConfigRaiseExceptionViewPath()
    {
        $modulesConfig = require './tests/config/wrong_config_autoload_view_path.php';
        (new Config($modulesConfig, './tests/config/{,*.}{global,local}.php'))
                ->getConfig();
    }
    
    /**
     * @expectedExceptionMessage Config view path for "not_exist" module is invalid
     * @expectedException Phalex\Config\Exception\RuntimeException
     */
    public function testGetConfigRaiseExceptionViewPathNotExisted()
    {
        $modulesConfig = require './tests/config/wrong_config_autoload_view_path_not_exist.php';
        (new Config($modulesConfig, './tests/config/{,*.}{global,local}.php'))
                ->getConfig();
    }
}
