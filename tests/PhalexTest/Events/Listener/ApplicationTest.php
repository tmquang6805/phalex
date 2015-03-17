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
            'route'       => '/test-boot/:controller/:action',
            'definitions' => [
                'controller' => 1,
                'action'     => 2,
                'module'     => 'Application',
                'namespace'  => 'Application\\Controller'
            ],
            'methods'     => ['GET', 'POST']
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/test-boot/static/route';

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
        $mockDi = $this->getMock(Di::class, ['set', 'get'], [[]]);
        $mockDi->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config(require './tests/config/config.result.php')));
        $mockDi->expects($this->atLeastOnce())
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
        $module = 'Application';
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
        $appListen->beforeStartModule($mockEvent, $mockApp, $module);

        $this->assertInstanceOf(View::class, $mockDi->get('view'));
        $this->assertInstanceOf(PhalconView::class, $mockDi->get('view'));
        $view    = $mockDi->get('view');
        $this->assertEquals($config['view']['Application'], rtrim($view->getViewsDir(), '/'));
        $engines = [
            '.phtml',
            '.volt',
        ];
        $this->assertEquals($engines, array_keys($view->getRegisteredEngines()));
        $this->assertEquals($config['url'][$module]['uri'], $mockDi->get('url')->getBaseUri());
        $this->assertEquals($config['url'][$module]['static'], $mockDi->get('url')->getStaticBaseUri());
    }

    /**
     * @group listener
     * @expectedException \Phalex\Events\Exception\RuntimeException
     * @expectedExceptionMessage Not found compiled folder for volt engine
     */
    public function testBeforeStartModuleWithVoltNotFound()
    {
        $config         = require './tests/config/config.result.php';
        $module         = 'Application';
        $config['volt'] = [
            $module => [
                'path' => __DIR__ . '/../compiled/',
            ],
        ];

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
        $appListen->beforeStartModule($mockEvent, $mockApp, $module);
    }

    /**
     * @group listener
     * @expectedException \Phalex\Events\Exception\RuntimeException
     * @expectedExceptionMessage Compiled folder is not writable
     */
    public function testBeforeStartModuleWithVoltNotWritable()
    {
        $folder         = 'tests/module/Application/compiled';
        chmod($folder, 0555);
        $config         = require './tests/config/config.result.php';
        $module         = 'Application';
        $config['volt'] = [
            $module => [
                'path' => $folder,
            ],
        ];

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
        $appListen->beforeStartModule($mockEvent, $mockApp, $module);
    }

    /**
     * @group listener
     */
    public function testBeforeStartModuleWithVoltSuccess()
    {
        $folder = 'tests/module/Application/compiled';
        chmod($folder, 0777);
        $config = require './tests/config/config.result.php';
        $module = 'Application';

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
        $appListen->beforeStartModule($mockEvent, $mockApp, $module);
    }
}
