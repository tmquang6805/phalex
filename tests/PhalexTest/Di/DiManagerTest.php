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
use Phalex\Mvc\Router;
use Phalcon\Http\Request as PhalconRequest;
use Phalcon\Config;
use Phalcon\Mvc\Router\Route;

/**
 * Description of DiManagerTest
 *
 * @author quangtm
 */
class DiManagerTest extends TestCase
{
    /**
     * @expectedException \Phalex\Di\Exception\RuntimeException
     * @expectedExceptionMessage Cannot init DI for router. Cannot find router configuration
     */
    public function testInitRouterDiRaiseExeptionRouterConfig()
    {
        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockDi->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config([])));

        (new DiManager($mockDi))->initRouterDi();
    }

    public function testInitRouterSuccess()
    {
        Route::reset();
        $config = [
            'router' => [
                'home' => [
                    'route'       => '/',
                    'definitions' => [
                        'controller' => 'index',
                        'action'     => 'index',
                        'module'     => 'Application',
                        'namespace'  => 'Application\\Controller'
                    ],
                    'methods'     => ['get', 'put']
                ],
            ]
        ];
        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockDi->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));

        $mockDi->expects($this->once())
                ->method('set')
                ->with('router');

        $diManager = new DiManager($mockDi);
        $diManager->initRouterDi();
    }

    public function testAddRouteHttpMethods()
    {
        Route::reset();
        $config = [
            'router' => [
                'home' => [
                    'route'       => '/',
                    'definitions' => [
                        'controller' => 'index',
                        'action'     => 'index',
                        'module'     => 'Application',
                        'namespace'  => 'Application\\Controller'
                    ],
                    'methods'     => ['get', 'put']
                ],
            ]
        ];
        $di     = new Di($config);
        $di->set('request', function () {
            return new PhalconRequest();
        });
        $diManager = new DiManager($di);
        $diManager->initRouterDi();
        $this->assertInstanceOf(Router::class, $diManager->getDi()->get('router'));
        $router    = $diManager->getDi()->get('router');

        $httpMethods = [
            'GET'     => true,
            'POST'    => false,
            'PUT'     => true,
            'PATCH'   => false,
            'OPTIONS' => false,
            'DELETE'  => false,
        ];
        foreach ($httpMethods as $httpMethod => $isMatched) {
            $_SERVER['REQUEST_METHOD'] = $httpMethod;
            $router->handle('/');
            $this->assertEquals($isMatched, $router->wasMatched());
            if ($router->wasMatched()) {
                $this->assertEquals('index', $router->getControllerName());
                $this->assertEquals('index', $router->getActionName());
                $this->assertEquals('Application\\Controller', $router->getNamespaceName());
                $this->assertEquals('Application', $router->getModuleName());
            }
        }
    }
}
