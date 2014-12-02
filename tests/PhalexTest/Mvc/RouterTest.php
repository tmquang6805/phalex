<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Mvc;

use Phalex\Mvc\Router;
use Phalcon\Mvc\Router\Route;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Description of RouterTest
 *
 * @author quangtm
 */
class RouterTest extends TestCase
{
    public function testConstruct()
    {
        Route::reset();
        $router = new Router();
        $router->add('/static/route');
        $router->notFound(array(
            'module'     => 'module',
            'namespace'  => 'namespace',
            'controller' => 'controller',
            'action'     => 'action'
        ));
        $router->handle();
        $this->assertEquals($router->getControllerName(), 'controller');
        $this->assertEquals($router->getActionName(), 'action');
        $this->assertEquals($router->getModuleName(), 'module');
        $this->assertEquals($router->getNamespaceName(), 'namespace');

        return $router;
    }

    /**
     * @depends testConstruct
     * @param Router $router
     */
    public function testRemoveExtraSlashes(Router $router)
    {
        $router->add("/:controller", [
            "controller" => 1,
        ]);

        $router->add("/:controller/:action/:params", [
            "controller" => 1,
            "action"     => 2,
            'params'     => 3,
        ]);
        $routes = array(
            '/index/'          => array(
                'controller' => 'index',
                'action'     => 'index',
            ),
            '/session/start/'  => array(
                'controller' => 'session',
                'action'     => 'start'
            ),
            '/users/edit/100/' => array(
                'controller' => 'users',
                'action'     => 'edit'
            ),
        );
        foreach ($routes as $route => $paths) {
            $router->handle($route);
            $this->assertTrue($router->wasMatched());
            $this->assertEquals($paths['controller'], $router->getControllerName());
            $this->assertEquals($paths['action'], $router->getActionName());
        }
    }

    /**
     * @depends testConstruct
     * @param Router $router
     */
    public function testUriSource(Router $router)
    {
        $_SERVER['REQUEST_URI'] = '/some/route';
        $this->assertEquals($router->getRewriteUri(), '/some/route');
        $_SERVER['REQUEST_URI'] = '/some/route?x=1';
        $this->assertEquals($router->getRewriteUri(), '/some/route');
    }

    /**
     * @expectedException \Phalex\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Not found required configs for router. Maybe miss "route" or "definitions"
     */
    public function testAddRouteRaiseException()
    {
        (new Router())->addRoute('test', []);
    }

    public function testAddRouteBasic()
    {
        Route::reset();
        $router = new Router();
        $routes = [
            'home'              => [
                'route'       => '/',
                'definitions' => [
                    'controller' => 'index',
                    'action'     => 'index',
                    'module'     => 'Application',
                    'namespace'  => 'Application\\Controller'
                ],
            ],
            'controller'        => [
                'route'       => '/:controller',
                'definitions' => [
                    'controller' => 1,
                    'action'     => 'index',
                    'module'     => 'Application',
                    'namespace'  => 'Application\\Controller'
                ],
            ],
            'controller/action' => [
                'route'       => '/:controller/:action',
                'definitions' => [
                    'controller' => 1,
                    'action'     => 2,
                    'module'     => 'Application',
                    'namespace'  => 'Application\\Controller'
                ],
            ]
        ];

        foreach ($routes as $name => $route) {
            $router->addRoute($name, $route);
        }
        unset($name, $route);

        $paths = [
            '/'                  => [
                'route_name' => 'home',
                'controller' => 'index',
                'action'     => 'index',
            ],
            '/index'             => [
                'route_name' => 'controller',
                'controller' => 'index',
                'action'     => 'index',
            ],
            '/application/test'  => [
                'route_name' => 'controller/action',
                'controller' => 'application',
                'action'     => 'test',
            ],
            '/application/test/' => [
                'route_name' => 'controller/action',
                'controller' => 'application',
                'action'     => 'test',
            ],
        ];

        foreach ($paths as $path => $opt) {
            $router->handle($path);
            $this->assertTrue($router->wasMatched());
            $this->assertEquals($opt['controller'], $router->getControllerName());
            $this->assertEquals($opt['action'], $router->getActionName());
            $this->assertEquals($opt['route_name'], $router->getMatchedRoute()->getName());
        }
    }

    public function testAddRouteHttpMethods()
    {
        $di = new \Phalcon\DI();
        $di->set('request', function () {
            return new \Phalcon\Http\Request();
        });
        
        Route::reset();
        $router = new Router();
        $router->setDI($di);
        $routes = [
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
//            'controller'        => [
//                'route'       => '/:controller',
//                'definitions' => [
//                    'controller' => 1,
//                    'action'     => 'index',
//                    'module'     => 'Application',
//                    'namespace'  => 'Application\\Controller'
//                ],
//            ],
//            'controller/action' => [
//                'route'       => '/:controller/:action',
//                'definitions' => [
//                    'controller' => 1,
//                    'action'     => 2,
//                    'module'     => 'Application',
//                    'namespace'  => 'Application\\Controller'
//                ],
//            ]
        ];

        foreach ($routes as $name => $route) {
            $router->addRoute($name, $route);
        }

        $paths = [
            '/' => [
                [
                    'method'  => 'GET',
                    'matched' => true,
                ],
                [
                    'method'  => 'POST',
                    'matched' => false,
                ],
                [
                    'method'  => 'PUT',
                    'matched' => true,
                ],
                [
                    'method'  => 'PATCH',
                    'matched' => false,
                ],
                [
                    'method'  => 'OPTIONS',
                    'matched' => false,
                ],
                [
                    'method'  => 'DELETE',
                    'matched' => false,
                ],
            ],
//            '/index'             => [
//                'route_name' => 'controller',
//                'controller' => 'index',
//                'action'     => 'index',
//            ],
//            '/application/test'  => [
//                'route_name' => 'controller/action',
//                'controller' => 'application',
//                'action'     => 'test',
//            ],
//            '/application/test/' => [
//                'route_name' => 'controller/action',
//                'controller' => 'application',
//                'action'     => 'test',
//            ],
        ];

        foreach ($paths as $path => $opts) {
            foreach ($opts as $value) {
                $_SERVER['REQUEST_METHOD'] = $value['method'];
                $router->handle($path);
                $this->assertEquals($value['matched'], $router->wasMatched());
            }
        }

//        foreach ($paths as $path => $opt) {
//            $this->assertEquals($opt['controller'], $router->getControllerName());
//            $this->assertEquals($opt['action'], $router->getActionName());
//            $this->assertEquals($opt['route_name'], $router->getMatchedRoute()->getName());
//        }
    }
}
