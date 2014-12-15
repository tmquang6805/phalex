<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Events;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Events\Listener;
use Phalex\Events\Listener\Application as ListenerApplication;
use Phalex\Events\Listener\Dispatch as ListenerDispatch;
use Phalex\Di\Di;
use Phalcon\Events\Manager as EM;
use Phalcon\Mvc\Application as PhalconApp;
use Phalex\Mvc\Dispatcher;

/**
 * Description of ListenerTest
 *
 * @author quangtm
 */
class ListenerTest extends TestCase
{
    /**
     *
     * @var Listener
     */
    private $listener;
    
    /**
     *
     * @var EM
     */
    private $em;

    protected function setUp()
    {
        $this->em = new EM();
        
        $di = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $di->expects($this->once())
                ->method('get')
                ->will($this->returnValue($this->em));

        $this->listener = new Listener($di);
    }
    
    protected function tearDown()
    {
        $this->em->detachAll();
    }
    
    public function testListenApplicationEvents()
    {
        $appMock = $this->getMock(PhalconApp::class);
        
        $listener = $this->getMockBuilder(ListenerApplication::class)
                ->getMock();
        $listener->expects($this->once())
                ->method('boot');
        $listener->expects($this->once())
                ->method('beforeStartModule');
        $listener->expects($this->once())
                ->method('afterStartModule');
        $listener->expects($this->once())
                ->method('beforeHandleRequest');
        $listener->expects($this->once())
                ->method('afterHandleRequest');
        $this->listener->listenApplicationEvents($listener);
        $this->em->fire('application:boot', null, null);
        $this->em->fire('application:beforeStartModule', $appMock, null);
        $this->em->fire('application:afterStartModule', $appMock, null);
        $this->em->fire('application:beforeHandleRequest', $appMock, null);
        $this->em->fire('application:afterHandleRequest', $appMock, null);
    }
    
    public function testListenDispatchEvents()
    {
        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
                ->disableOriginalConstructor()
                ->getMock();
        
        $listener = $this->getMockBuilder(ListenerDispatch::class)
                ->getMock();
        $listener->expects($this->once())
                ->method('beforeDispatchLoop');
        $listener->expects($this->once())
                ->method('beforeExecuteRoute');
        $listener->expects($this->once())
                ->method('afterExecuteRoute');
        $listener->expects($this->once())
                ->method('beforeNotFoundAction');
        $listener->expects($this->once())
                ->method('beforeException');
        $listener->expects($this->once())
                ->method('afterDispatchLoop');
        $this->listener->listenDispatchEvents($listener);
        $this->em->fire('dispatch:beforeDispatchLoop', $dispatcherMock, null);
        $this->em->fire('dispatch:beforeExecuteRoute', $dispatcherMock, null);
        $this->em->fire('dispatch:afterExecuteRoute', $dispatcherMock, null);
        $this->em->fire('dispatch:beforeNotFoundAction', $dispatcherMock, null);
        $this->em->fire('dispatch:beforeException', $dispatcherMock, null);
        $this->em->fire('dispatch:afterDispatchLoop', $dispatcherMock, null);
    }
}
