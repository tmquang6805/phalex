<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Di;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Di\Di;
use Phalcon\DI\FactoryDefault;
use Phalcon\Config;
use Phalcon\Events\Manager;

/**
 * Description of DiManagerTest
 *
 * @author quangtm
 */
class DiTest extends TestCase
{
    /**
     * @return DiManager
     */
    public function testConstruct()
    {
        $entireAppConfig = require './tests/config/config.result.php';
        $di = new Di($entireAppConfig);
        $this->assertInstanceOf(FactoryDefault::class, $di);
        
        // Assert has key config, and config is entire application config
        $this->assertTrue(isset($di['config']));
        $this->assertTrue($di->has('config'));
        $this->assertInstanceOf(Config::class, $di['config']);
        $this->assertEquals($entireAppConfig, $di['config']->toArray());
        
        // Assert has key eventsManager
        $this->assertTrue(isset($di['eventsManager']));
        $this->assertTrue($di->has('eventsManager'));
        $this->assertInstanceOf(Manager::class, $di->get('eventsManager'));
        $ev = $di->get('eventsManager');
        $this->assertTrue($ev->arePrioritiesEnabled());
        $this->assertTrue($ev->isCollecting());
    }
}
