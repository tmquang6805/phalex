<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Mvc;

use Phalex\Mvc\Router;
use Phalcon\Mvc\Router\Route;
use Phalcon\DI as PhalconDi;
use Phalcon\Http\Request as PhalconRequest;
use PHPUnit_Framework_TestCase as TestCase;
use Application\Router\BeforeMatchFail;
use Phalex\Mvc\Router\BeforeMatchInterface;
use Phalex\Mvc\Exception\RuntimeException;
use Application\Router\BeforeMatchMock;

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

    private function getDi()
    {
        $di = new PhalconDi();
        $di->set('request', function () {
            return new PhalconRequest();
        });
        return $di;
    }

    public function testAddRouteHttpMethods()
    {
        Route::reset();
        $router = new Router();
        $router->setDI($this->getDi());
        $routes = [
            'home'       => [
                'route'       => '/',
                'definitions' => [
                    'controller' => 'index',
                    'action'     => 'index',
                    'module'     => 'Application',
                    'namespace'  => 'Application\\Controller'
                ],
                'methods'     => ['get', 'put']
            ],
            'controller' => [
                'route'       => '/:controller',
                'definitions' => [
                    'controller' => 1,
                    'action'     => 'index',
                    'module'     => 'Application',
                    'namespace'  => 'Application\\Controller'
                ],
                'methods'     => ['get', 'options']
            ],
        ];

        foreach ($routes as $name => $route) {
            $router->addRoute($name, $route);
        }

        $paths = [
            '/'      => [
                'GET'     => true,
                'POST'    => false,
                'PUT'     => true,
                'PATCH'   => false,
                'OPTIONS' => false,
                'DELETE'  => false,
            ],
            '/index' => [
                'GET'     => true,
                'POST'    => false,
                'PUT'     => false,
                'PATCH'   => false,
                'OPTIONS' => true,
                'DELETE'  => false,
            ],
        ];

        foreach ($paths as $path => $opts) {
            foreach ($opts as $httpMethod => $isMatched) {
                $_SERVER['REQUEST_METHOD'] = $httpMethod;
                $router->handle($path);
                $this->assertEquals($isMatched, $router->wasMatched());
            }
        }
    }

    /**
     * @group route_host
     */
    public function testHostnameRouter()
    {
        Route::reset();
        $router = new Router();
        $router->setDI($this->getDi());

        $router->addRoute('localhost/edit', [
            'route'       => '/edit',
            'definitions' => [
                'controller' => 'posts-local',
                'action'     => 'edit-local'
            ],
        ]);
        $router->addRoute('sub-example/edit', [
            'route'       => '/edit',
            'definitions' => [
                'controller' => 'posts-example',
                'action'     => 'edit-example'
            ],
            'host_name'   => 'sub.example.com'
        ]);
        $router->addRoute('sub1-example/edit', [
            'route'       => '/edit',
            'definitions' => [
                'controller' => 'posts-sub',
                'action'     => 'edit-sub'
            ],
            'host_name'   => 'sub1.example.com'
        ]);
        $routes = array(
            array(
                'hostname'   => null,
                'controller' => 'posts-local'
            ),
            array(
                'hostname'   => 'sub.example.com',
                'controller' => 'posts-example'
            ),
            array(
                'hostname'   => 'sub1.example.com',
                'controller' => 'posts-sub'
            ),
        );
        foreach ($routes as $route) {
            $_SERVER['HTTP_HOST'] = $route['hostname'];
            $router->handle('/edit');
            $this->assertTrue($router->wasMatched());
            $this->assertEquals($route['controller'], $router->getControllerName());
            $this->assertEquals($route['hostname'], $router->getMatchedRoute()->getHostname());
        }
    }

    /**
     * @group convert
     */
    public function testConvertions()
    {
        require_once 'tests/module/Application/src/Router/ConvertId.php';
        Route::reset();
        $router = new Router();
        $router->setDI($this->getDi());

        $router->addRoute('edit-closure', [
            'route'       => '/edit/([1-9][0-9]*)',
            'definitions' => [
                'controller' => 'posts',
                'action'     => 'edit',
                'id'         => 1
            ],
            'convertions' => [
                'id' => function ($id) {
                    return intval($id);
                }
            ],
        ]);
        $router->addRoute('edit-classname', [
            'route'       => '/edit2/([1-9][0-9]*)',
            'definitions' => [
                'controller' => 'posts2',
                'action'     => 'edit2',
                'id'         => 1
            ],
            'convertions' => [
                'id' => [
                    'class_name' => \Application\Router\ConvertId::class,
                ]
            ],
        ]);
        $routes = [
            '/edit/100',
            '/edit2/100'
        ];
        foreach ($routes as $route) {
            $router->handle($route);
            $this->assertTrue($router->wasMatched());
            $id = $router->getParams()['id'];
            $this->assertInternalType('int', $id);
            $this->assertTrue($id === 100);
        }
    }

    /**
     * @group convert
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage Config router convert miss "class_name"
     */
    public function testConvertionsRaiseMissConfig()
    {
        Route::reset();
        $router = new Router();
        $router->setDI($this->getDi());

        $router->addRoute('edit-classname', [
            'route'       => '/edit2/([1-9][0-9]*)',
            'definitions' => [
                'controller' => 'posts2',
                'action'     => 'edit2',
                'id'         => 1
            ],
            'convertions' => [
                'id' => []
            ],
        ]);
        $router->handle('/edit2/100');
    }

    /**
     * @group convert
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage "A\B\Convert" is not existed
     */
    public function testConvertionsRaiseClassNotExists()
    {
        Route::reset();
        $router = new Router();
        $router->setDI($this->getDi());

        $router->addRoute('edit-classname', [
            'route'       => '/edit2/([1-9][0-9]*)',
            'definitions' => [
                'controller' => 'posts2',
                'action'     => 'edit2',
                'id'         => 1
            ],
            'convertions' => [
                'id' => [
                    'class_name' => 'A\\B\\Convert'
                ]
            ],
        ]);
        $router->handle('/edit2/100');
    }

    /**
     * @group convert
     * @expectedException \Phalex\Mvc\Exception\RuntimeException
     * @expectedExceptionMessage "Application\Router\Temp" must be implemented "Phalex\Mvc\Router\ConvertingInterface"
     */
    public function testConvertionsRaiseInvalidClass()
    {
        require_once 'tests/module/Application/src/Router/Temp.php';
        Route::reset();
        $router = new Router();
        $router->setDI($this->getDi());

        $router->addRoute('edit-classname', [
            'route'       => '/edit2/([1-9][0-9]*)',
            'definitions' => [
                'controller' => 'posts2',
                'action'     => 'edit2',
                'id'         => 1
            ],
            'convertions' => [
                'id' => [
                    'class_name' => \Application\Router\Temp::class
                ]
            ],
        ]);
        $router->handle('/edit2/100');
    }

    /**
     * @group before_match
     */
    public function testBeforeMatch()
    {
        require_once 'tests/module/Application/src/Router/BeforeMatchMock.php';
        Route::reset();
        $trace  = 0;
        $router = new Router();

        $router->addRoute('fail', [
            'route'        => '/route-fail',
            'definitions'  => [
                'controller' => 'fail',
                'index'      => 'fail'
            ],
            'before_match' => function () use (&$trace) {
                $trace++;
                return false;
            },
        ]);

        $router->addRoute('success', [
            'route'        => '/route-success',
            'definitions'  => [
                'controller' => 'success',
                'index'      => 'success'
            ],
            'before_match' => [
                'class_name' => BeforeMatchMock::class
            ],
        ]);
        $router->handle();
        $this->assertFalse($router->wasMatched());
        $router->handle('/route-fail');
        $this->assertFalse($router->wasMatched());
        $this->assertEquals($trace, 1);
        $router->handle('/route-success');
        $this->assertTrue($router->wasMatched());
    }
    
    /**
     * @group before_match
     */
    public function testBeforeMatchRaiseInvalidClass()
    {
        require_once 'tests/module/Application/src/Router/BeforeMatchFail.php';
        $excMsg = sprintf('"%s" must be implemented "%s"', BeforeMatchFail::class, BeforeMatchInterface::class);
        $this->setExpectedException(RuntimeException::class, $excMsg);
        Route::reset();
        $router = new Router();
        $router->setDI($this->getDi());

        $router->addRoute('edit-classname', [
            'route'       => '/edit2/([1-9][0-9]*)',
            'definitions' => [
                'controller' => 'posts2',
                'action'     => 'edit2',
                'id'         => 1
            ],
            'before_match' => [
                'class_name' => BeforeMatchFail::class
            ],
        ]);
        $router->handle('/edit2/100');
    }
}
