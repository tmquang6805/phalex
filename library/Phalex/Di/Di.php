<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Di;

use Phalcon\DI\FactoryDefault;
use Phalcon\Config;
use Phalcon\Events\Manager as EventsManager;
use Phalex\Mvc\Dispatcher;
use Phalcon\Mvc\View;

/**
 * Control setting/getting Phalcon DI
 *
 * @author quangtm
 */
class Di extends FactoryDefault
{
    public function __construct(array $entireAppConfig)
    {
        parent::__construct();
        $this->set('config', new Config($entireAppConfig), true);
        $this->setEventsManager();
        $this->set('dispatcher', new Dispatcher($this), true);
        $this->set('view', new View());
    }
    
    /**
     * Override events manager default in Phalcon
     * @return \Phalex\Di\Di
     */
    protected function setEventsManager()
    {
        $ev = new EventsManager();
        $ev->enablePriorities(true);
        $ev->collectResponses(true);
        $this->set('eventsManager', $ev, true);
        return $this;
    }
}
