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
use Phalcon\Events\Event;
use Phalcon\Mvc\Application as PhalconApp;
use Phalcon\Config;
use Phalcon\Mvc\View\Engine;
use Phalcon\Mvc\View as PhalconView;

/**
 * Description of ApplicationTest
 *
 * @author quangtm
 */
class ApplicationTest extends TestCase
{
    public function testBoot()
    {
        $this->markTestIncomplete();
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
        $view = $mockDi->get('view');
        $this->assertEquals($config['view']['Application'], rtrim($view->getViewsDir(), '/'));
        $engines = [
            '.phtml' => Engine\Php::class,
            '.volt'  => Engine\Volt::class,
        ];
        $this->assertEquals($engines, $view->getRegisteredEngines());
    }
}
