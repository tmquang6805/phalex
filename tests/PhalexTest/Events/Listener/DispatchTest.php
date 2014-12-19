<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Events\Listener;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Events\Listener\Dispatch;
use Phalex\Mvc\Dispatcher;
use Phalex\Mvc\View;
use Phalcon\Events\Event;
use Phalcon\Mvc\Controller;
use Mockery as m;

/**
 * Description of DispatchTest
 *
 * @author quangtm
 */
class DispatchTest extends TestCase
{
    /**
     * @expectedException Phalcon\Mvc\Dispatcher\Exception
     * @expectedExceptionMessage Cannot match route
     * @group listener
     */
    public function testBeforeDispatchLoopRaiseException()
    {
        $eventMock      = $this->getMockBuilder(Event::class)
                ->disableOriginalConstructor()
                ->getMock();
        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
                ->disableOriginalConstructor()
                ->getMock();
        $dispatcherMock->expects($this->once())
                ->method('getControllerName')
                ->will($this->returnValue('error'));
        $dispatcherMock->expects($this->once())
                ->method('getActionName')
                ->will($this->returnValue('not-found'));
        (new Dispatch())->beforeDispatchLoop($eventMock, $dispatcherMock);
    }

    /**
     * @group listener
     */
    public function testBeforeDispatchLoop()
    {
        $eventMock      = $this->getMockBuilder(Event::class)
                ->disableOriginalConstructor()
                ->getMock();
        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
                ->disableOriginalConstructor()
                ->getMock();
        $dispatcherMock->expects($this->once())
                ->method('getControllerName')
                ->will($this->returnValue('index'));
        $dispatcherMock->expects($this->once())
                ->method('getActionName')
                ->will($this->returnValue('product-detail'));
        $dispatcherMock->expects($this->once())
                ->method('setActionName');
        $dispatcherMock->expects($this->once())
                ->method('getParams')
                ->will($this->returnValue(['a', 'value', 'test', 123]));
        $dispatcherMock->expects($this->once())
                ->method('setParams');
        (new Dispatch())->beforeDispatchLoop($eventMock, $dispatcherMock);
    }

    public function testBeforeExecuteRoute()
    {
        $eventMock      = m::mock(Event::class);
        $dispatcherMock = m::mock(Dispatcher::class);
        $controllerMock = m::mock(Controller::class);
        $viewMock = $this->getMockBuilder(View::class)
                ->disableOriginalConstructor()
                ->getMock();
        
        $viewMock->expects($this->once())
                ->method('pick');
        
        $controllerMock->view = $viewMock;
        
        $dispatcherMock->shouldReceive('getActiveController')->andReturn($controllerMock);
        $dispatcherMock->shouldReceive('getControllerName')->andReturn('index');
        $dispatcherMock->shouldReceive('getActionName')->andReturn('productDetail');
        
        (new Dispatch())->beforeExecuteRoute($eventMock, $dispatcherMock);
    }
}
