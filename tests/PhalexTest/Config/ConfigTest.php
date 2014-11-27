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
        $modulesConfig = ArrayUtils::merge(require './tests/module/Application/config/module.config.php', require './tests/module/Backend/config/module.config.php');
        $globPaths     = [
            './tests/config/{,*.}{global}.php',
            './tests/config/local.php'
        ];

        $resultConfig = (new Config($modulesConfig, $globPaths))
                ->getConfig();
        $this->assertInternalType('array', $resultConfig);
        $this->assertEquals($expected, $resultConfig);
    }
}
