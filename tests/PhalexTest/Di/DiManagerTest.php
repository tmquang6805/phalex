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
use Phalcon\Config;
use Phalcon\Mvc\Router\Route;

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

    public function testInitRouterDiRaiseExceptionRouteInfo()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Cannot find "route" info');

        $entireAppConfig = require './tests/config/config.result.php';
        unset($entireAppConfig['router']['home']['route']);

        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockDi->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($entireAppConfig)));

        (new DiManager($mockDi))->initRouterDi();
    }

    public function testInitRouterDiRaiseExceptionDefinitionInfo()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Cannot find "definitions" info');

        $entireAppConfig = require './tests/config/config.result.php';
        unset($entireAppConfig['router']['home']['definitions']);

        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockDi->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($entireAppConfig)));

        (new DiManager($mockDi))->initRouterDi();
    }

    public function testSetConvertionsRaiseExceptionClassNotFound()
    {
        $this->setExpectedException(RuntimeException::class, 'Class "Application\Router\ConvertId" is not found');

        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();

        $mockRoute = $this->getMockBuilder(Route::class)
                ->disableOriginalConstructor()
                ->getMock();

        $info = [
            'route'       => '/news/([a-z0-9-]+)-([1-9][0-9]*)\.html',
            'definitions' => [
                'module'     => \Application::class,
                'namespace'  => \Application\Controller::class,
                'controller' => 'article',
                'action'     => 'detail',
                'title'      => 1,
                'id'         => 2
            ],
            'convertions' => [
                'id' => \Application\Router\ConvertId::class,
            ],
        ];

        $diManager  = new DiManager($mockDi);
        $reflection = new \ReflectionClass(DiManager::class);

        $setConvertionsMethod = $reflection->getMethod('setConvertions');
        $setConvertionsMethod->setAccessible(true);
        $setConvertionsMethod->invokeArgs($diManager, [$mockRoute, $info]);
    }

    public function testSetConvertionsRaiseExceptionClassNotImplement()
    {
        require './tests/module/Application/src/Router/ConvertId.php';
        $this->setExpectedException(RuntimeException::class, 'Class "Application\\Router\\ConvertId" is not implemented by "Phalex\\Router\\ConvertingInterface" interface');

        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        
        $mockDi->expects($this->once())
                ->method('has')
                ->will($this->returnValue(false));
        
        $mockDi->expects($this->once())
                ->method('set');
        
        $mockDi->expects($this->once())
                ->method('get')
                ->with('Application\\Router\\ConvertId')
                ->will($this->returnValue(new \ArrayObject()));

        $mockRoute = $this->getMockBuilder(Route::class)
                ->disableOriginalConstructor()
                ->getMock();

        $info = [
            'route'       => '/news/([a-z0-9-]+)-([1-9][0-9]*)\.html',
            'definitions' => [
                'module'     => \Application::class,
                'namespace'  => \Application\Controller::class,
                'controller' => 'article',
                'action'     => 'detail',
                'title'      => 1,
                'id'         => 2
            ],
            'convertions' => [
                'id' => \Application\Router\ConvertId::class,
            ],
        ];

        $diManager  = new DiManager($mockDi);
        $reflection = new \ReflectionClass(DiManager::class);

        $setConvertionsMethod = $reflection->getMethod('setConvertions');
        $setConvertionsMethod->setAccessible(true);
        $setConvertionsMethod->invokeArgs($diManager, [$mockRoute, $info]);
    }

}
