<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Di;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Di\DiManager;
use Phalex\Di\Di;
use Phalex\Di\Exception\RuntimeException;
use Phalex\Di\Exception\InvalidArgumentException;
use Phalex\Router\ConvertingInterface;
use Phalex\Router\BeforeMatchInterface;
use Phalcon\Config;
use Phalcon\Mvc\Router\Route;
use Phalcon\Mvc\Router;

/**
 * Description of DiManagerTest
 *
 * @author quangtm
 */
class DiManagerTest extends TestCase
{
    public function testInitRouterDiRaiseExeptionRouterConfig()
    {
        $this->setExpectedException(RuntimeException::class, 'Cannot init DI for router. Cannot find router configuration');

        $entireAppConfig = require './tests/config/config.result.php';
        unset($entireAppConfig['router']);

        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockDi->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($entireAppConfig)));

        (new DiManager($mockDi))->initRouterDi();
    }
}
