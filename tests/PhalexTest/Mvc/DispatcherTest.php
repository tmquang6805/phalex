<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Mvc\Dispatcher;
use Phalex\Di\Di;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher as PhalconDispatcher;

/**
 * Description of DispatcherTest
 *
 * @author quangtm
 */
class DispatcherTest extends TestCase
{
    public function testConstructor()
    {
        $di         = new Di(require './tests/config/config.result.php');
        $dispatcher = new Dispatcher($di);
        $this->assertInstanceOf(PhalconDispatcher::class, $dispatcher);
        $di2 = $dispatcher->getDI();
        $this->assertInstanceOf(Di::class, $di2);
        $this->assertTrue(isset($di2['dispatchEventsManager']));
        $this->assertInstanceOf(EventsManager::class, $di2->get('dispatchEventsManager'));
    }
}
