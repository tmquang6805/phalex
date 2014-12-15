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
use PhalexTest\Di\Mock\DateObject;
use PhalexTest\Di\Mock\FactoryMock;

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

    /**
     * @group service_invokables
     */
    public function testInitInvokableServices()
    {
        $config = [
            'service_manager' => [
                'invokables' => [
                    'ArrayObject' => \ArrayObject::class
                ]
            ],
        ];
        $diMock = $this->getMockBuilder(Di::class)
                ->setConstructorArgs($config)
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $diMock->expects($this->exactly(count($config['service_manager']['invokables'])))
                ->method('set');
        $di     = new DiManager($diMock);
        $di->initInvokableServices();
    }

    /**
     * @expectedException \Phalex\Di\Exception\UnexpectedValueException
     * @expectedExceptionMessage Config for invokable service "ArrayObject" must be string data type
     * @group service_invokables
     */
    public function testInitInvokableServicesRaiseException()
    {
        $config = [
            'service_manager' => [
                'invokables' => [
                    'ArrayObject' => new \ArrayObject()
                ]
            ],
        ];
        $diMock = $this->getMockBuilder(Di::class)
                ->setConstructorArgs($config)
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $di     = new DiManager($diMock);
        $di->initInvokableServices();
    }

    /**
     * @group service_invokables
     */
    public function testInitInvokableServicesChecked()
    {
        $config = [
            'service_manager' => [],
        ];
        $diMock = $this->getMockBuilder(Di::class)
                ->setConstructorArgs($config)
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $diMock->expects($this->never())
                ->method('set');
        $di     = new DiManager($diMock);
        $di->initInvokableServices();

        $config = [
            'service_manager' => [
                'invokables' => [
                    'ArrayObject' => \ArrayObject::class
                ]
            ],
        ];
        $diMock = new Di($config);
        $di     = new DiManager($diMock);
        $di->initInvokableServices();
        $this->assertInstanceOf(Di::class, $di->getDi());
        $this->assertTrue($di->getDi()->has('ArrayObject'));
        $this->assertInstanceOf(\ArrayObject::class, $di->getDi()->get('ArrayObject'));
    }

    public function supplyTestSharedInvokableServices()
    {
        return [
            [
                [
                    'service_manager' => [
                        'invokables' => [
                            'myObject' => DateObject::class
                        ],
                        'shared'     => [
                            'myObject' => false
                        ],
                    ],
                ],
                false
            ],
            [
                [
                    'service_manager' => [
                        'invokables' => [
                            'myObject' => DateObject::class
                        ],
                        'shared'     => [
                            'myObject' => true
                        ],
                    ],
                ],
                true
            ],
        ];
    }

    /**
     * @dataProvider supplyTestSharedInvokableServices
     * @group service_shared
     */
    public function testSharedInvokableServices($config, $equal)
    {
        $diMock = new Di($config);
        $di     = new DiManager($diMock);
        $di->initInvokableServices();

        $obj1 = $di->getDi()->get('myObject');
        $this->assertInstanceOf(DateObject::class, $obj1);
        usleep(10000);
        $obj2 = $di->getDi()->get('myObject');
        $this->assertInstanceOf(DateObject::class, $obj2);
        $this->assertEquals($equal, $obj1->time === $obj2->time);
    }

    public function supplyTestInitFactoriedServices()
    {
        return [
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObj' => function ($di) {
                                $this->assertInstanceOf(Di::class, $di);
                                return new DateObject;
                            }
                        ],
                    ],
                ],
            ],
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObj' => FactoryMock::class
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @group service_factories
     * @dataProvider supplyTestInitFactoriedServices
     */
    public function testInitFactoriedServices($config)
    {
        $diMock = $this->getMockBuilder(Di::class)
                ->setConstructorArgs($config)
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $diMock->expects($this->exactly(count($config['service_manager']['factories'])))
                ->method('set');
        $di     = new DiManager($diMock);
        $di->initFactoriedServices();
    }

    public function supplyTestInitFactoriesServicesRaiseException()
    {
        return [
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObj' => new \stdClass()
                        ]
                    ],
                ],
            ],
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObj' => []
                        ]
                    ],
                ],
            ],
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObj' => 1.2
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @group service_factories
     * @dataProvider supplyTestInitFactoriesServicesRaiseException
     * @expectedException \Phalex\Di\Exception\UnexpectedValueException
     * @expectedExceptionMessage Config for factories service "myObj" must be string or callable
     */
    public function testInitFactoriesServicesRaiseException($config)
    {
        $diMock = $this->getMockBuilder(Di::class)
                ->setConstructorArgs($config)
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $di     = new DiManager($diMock);
        $di->initFactoriedServices();
    }

    /**
     * @group service_factories
     * @expectedException \Phalex\Di\Exception\RuntimeException
     * @expectedExceptionMessage Class "ArrayObject" must be implemented "Phalex\Di\DiFactoryInterface"
     */
    public function testInitFactoriesServicesRaiseExceptionInterface()
    {
        $config = [
            'service_manager' => [
                'factories' => [
                    'myObj' => \ArrayObject::class
                ],
            ],
        ];
        $diMock = new Di($config);
        $di     = new DiManager($diMock);
        $di->initFactoriedServices();
        $di->getDi()->get('myObj');
    }

    public function supplyTestInitFactoriesServicesChecked()
    {
        return [
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObj' => function ($di) {
                                $this->assertInstanceOf(Di::class, $di);
                                return new DateObject;
                            }
                        ],
                    ],
                ],
            ],
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObj' => FactoryMock::class
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @group service_factories
     * @dataProvider supplyTestInitFactoriesServicesChecked
     */
    public function testInitFactoriesServicesChecked($config)
    {
        $diMock = new Di($config);
        $di     = new DiManager($diMock);
        $di->initFactoriedServices();
        $this->assertInstanceOf(Di::class, $di->getDi());
        $this->assertTrue($di->getDi()->has('myObj'));
        $this->assertInstanceOf(DateObject::class, $di->getDi()->get('myObj'));

        $config = [
            'service_manager' => [],
        ];
        $diMock = $this->getMockBuilder(Di::class)
                ->setConstructorArgs($config)
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $diMock->expects($this->never())
                ->method('set');
        $di     = new DiManager($diMock);
        $di->initFactoriedServices();
    }

    public function supplyTestSharedFactoriesServices()
    {
        return [
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObject' => function () {
                                return new DateObject();
                            }
                        ],
                        'shared'          => [
                            'myObject' => false
                        ],
                    ],
                ],
                false
            ],
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObject' => function () {
                                return new DateObject();
                            }
                        ],
                        'shared'          => [
                            'myObject' => true
                        ],
                    ],
                ],
                true
            ],
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObject' => FactoryMock::class
                        ],
                        'shared'    => [
                            'myObject' => false
                        ],
                    ],
                ],
                false
            ],
            [
                [
                    'service_manager' => [
                        'factories' => [
                            'myObject' => FactoryMock::class
                        ],
                        'shared'    => [
                            'myObject' => true
                        ],
                    ],
                ],
                true
            ],
        ];
    }

    /**
     * @dataProvider supplyTestSharedFactoriesServices
     * @group service_factories
     * @group service_shared
     */
    public function testSharedFactoriesServices($config, $equal)
    {
        $diMock = new Di($config);
        $di     = new DiManager($diMock);
        $di->initFactoriedServices();

        $obj1 = $di->getDi()->get('myObject');
        $this->assertInstanceOf(DateObject::class, $obj1);
        usleep(100000);
        $obj2 = $di->getDi()->get('myObject');
        $this->assertInstanceOf(DateObject::class, $obj2);
        $this->assertEquals($equal, $obj1->time === $obj2->time);
    }
    
    /**
     * @group service_invokables
     * @group service_factories
     */
    public function testInitServicesWithoutConfig()
    {
        $config = [];
        $diMock = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $diMock->expects($this->never())
                ->method('set');
        $di     = new DiManager($diMock);
        $di->initInvokableServices();
        $di->initFactoriedServices();
    }
}
