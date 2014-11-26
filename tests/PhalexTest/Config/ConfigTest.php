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

/**
 * Description of ConfigTest
 *
 * @author quangtm
 */
class ConfigTest extends TestCase
{
    private function getFiles($globPaths)
    {
        $files = [];
        if (!is_array($globPaths)) {
            $globPaths = [$globPaths];
        }

        foreach ($globPaths as $globPath) {
            foreach (glob($globPath, GLOB_BRACE) as $file) {
                array_push($files, $file);
            }
        }
        return $files;
    }

    public function supplyTestGetConfigBasic()
    {
        $modulesConfig = [];
        $glob          = './config/{,*.}{global,local}.php';
        $files         = $this->getFiles($glob);
        $result        = $modulesConfig;
        foreach ($files as $file) {
            $result = ArrayUtils::merge($result, require $file);
        }
        return [$modulesConfig, $glob, $result];
    }

    public function supplyTestConfigArrayGlobPaths()
    {
        $modulesConfig = [];
        $globPaths     = [
            './config/{,*.}{global}.php',
            './config/local.php'
        ];

        $files  = $this->getFiles($globPaths);
        $result = $modulesConfig;
        foreach ($files as $file) {
            $result = ArrayUtils::merge($result, require $file);
        }

        return [$modulesConfig, $globPaths, $result];
    }

    public function supplyTestConfig()
    {
        $modulesConfig = ArrayUtils::merge(require './module/Application/config/module.config.php', require './module/Backend/config/module.config.php');
        $globPaths     = [
            './config/{,*.}{global}.php',
            './config/local.php'
        ];

        $files  = $this->getFiles($globPaths);
        $result = $modulesConfig;
        foreach ($files as $file) {
            $result = ArrayUtils::merge($result, require $file);
        }

        return [$modulesConfig, $globPaths, $result];
    }

    public function supplyTestGetConfig()
    {
        $basic   = $this->supplyTestGetConfigBasic();
        $basic[] = null;

        $arrGlob   = $this->supplyTestConfigArrayGlobPaths();
        $arrGlob[] = null;

        $arrFull   = $this->supplyTestConfig();
        $arrFull[] = null;
        return[
            $basic,
            $arrGlob,
            $arrFull,
        ];
    }

    /**
     * @dataProvider supplyTestGetConfig
     * @param array $modulesConfig
     * @param array|string $globPath
     * @param array $expected
     * @param CacheInterface $cacheInstance
     */
    public function testGetConfig(array $modulesConfig, $globPath, $expected, CacheInterface $cacheInstance = null)
    {
        $resultConfig = (new Config($modulesConfig, $globPath, $cacheInstance))
                ->getConfig();
        $this->assertEquals($expected, $resultConfig);
    }
}
