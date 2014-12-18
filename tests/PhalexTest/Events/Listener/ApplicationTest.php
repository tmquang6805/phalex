<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Events\Listener;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Events\Listener\Application as ListenApp;
use Phalex\Mvc\View;
use Phalex\Di\Di;
use Phalex\Mvc\Router;
use Phalcon\Events\Event;
use Phalcon\Mvc\Application as PhalconApp;
use Phalcon\Config;
use Phalcon\Mvc\View\Engine;
use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\Router\Route;
use Phalcon\Http\Request as PhalconRequest;
use Mockery as m;

/**
 * Description of ApplicationTest
 *
 * @author quangtm
 */
class ApplicationTest extends TestCase
{
    private function getDi()
    {
        $di = new Di(require './tests/config/config.result.php');
        $di->set('request', function () {
            return new PhalconRequest();
        });
        return $di;
    }

    /**
     * @group listener
     * @expectedException Phalcon\Mvc\Dispatcher\Exception
     * @expectedExceptionMessage Cannot match route
     */
    public function testBootRaiseException()
    {
        Route::reset();
        $router = new Router($this->getDi());
        $router->add('/static/route');

        $appMock         = m::mock(PhalconApp::class);
        $appMock->router = $router;

        $eventMock = m::mock(Event::class);
        (new ListenApp())->boot($eventMock, $appMock);
    }

    /**
     * @group listener
     */
    public function testBootSuccess()
    {
        $di     = $this->getDi();
        Route::reset();
        $router = new Router($di);
        $router->addRoute('controller/action', [
            'route'       => '/:controller/:action',
            'definitions' => [
                'controller' => 1,
                'action'     => 2,
                'module'     => 'Application',
                'namespace'  => 'Application\\Controller'
            ],
            'methods'     => ['GET', 'POST']
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/static/route';

        $appMock         = m::mock(PhalconApp::class);
        $appMock->router = $router;

        $appMock->shouldReceive('getDI')
                ->andReturn($di);

        $eventMock = m::mock(Event::class);

        (new ListenApp())->boot($eventMock, $appMock);

        $this->assertTrue($di->has('matchedRoute'));
        $this->assertTrue(isset($di['matchedRoute']));
        $this->assertInstanceOf(Route::class, $di['matchedRoute']);
        $this->assertEquals('controller/action', $di['matchedRoute']->getName());
    }

    /**
     * @group listener
     */
    public function testBeforeStartModuleMock()
    {
        $mockDi = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockDi->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config(require './tests/config/config.result.php')));
        $mockDi->expects($this->once())
                ->method('set');

        $mockApp = $this->getMockBuilder(PhalconApp::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockApp->expects($this->once())
                ->method('getDI')
                ->will($this->returnValue($mockDi));

        $mockEvent = $this->getMockBuilder(Event::class)
                ->disableOriginalConstructor()
                ->getMock();

        $appListen = new ListenApp();
        $appListen->beforeStartModule($mockEvent, $mockApp, 'Application');
    }

    /**
     * @group listener
     */
    public function testBeforeStartModule()
    {
        $config = require './tests/config/config.result.php';
        $mockDi = new Di($config);

        $mockApp = $this->getMockBuilder(PhalconApp::class)
                ->disableOriginalConstructor()
                ->getMock();
        $mockApp->expects($this->once())
                ->method('getDI')
                ->will($this->returnValue($mockDi));

        $mockEvent = $this->getMockBuilder(Event::class)
                ->disableOriginalConstructor()
                ->getMock();

        $appListen = new ListenApp();
        $appListen->beforeStartModule($mockEvent, $mockApp, 'Application');

        $this->assertInstanceOf(View::class, $mockDi->get('view'));
        $this->assertInstanceOf(PhalconView::class, $mockDi->get('view'));
        $view    = $mockDi->get('view');
        $this->assertEquals($config['view']['Application'], rtrim($view->getViewsDir(), '/'));
        $engines = [
            '.phtml' => Engine\Php::class,
            '.volt'  => Engine\Volt::class,
        ];
        $this->assertEquals($engines, $view->getRegisteredEngines());
    }
}
