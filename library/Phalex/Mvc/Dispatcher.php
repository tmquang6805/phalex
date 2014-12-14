<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Phalcon\Mvc\Dispatcher as PhalconDispatcher;
use Phalcon\Events\Manager as EventsManager;
use Phalex\Di\Di;

/**
 * Description of Dispatcher
 *
 * @author quangtm
 */
class Dispatcher extends PhalconDispatcher
{
    /**
     *
     * @var Di
     */
    protected $di;


    public function __construct(Di $di)
    {
        parent::__construct();
        $this->di = $di;
        $eventsManager = new EventsManager();
        $eventsManager->enablePriorities(true);
        $eventsManager->collectResponses(true);
        
        $di->set('dispatchEventsManager', $eventsManager, true);
        $this->setEventsManager($eventsManager);
        $this->setDI($di);
        $this->setDefaultAction('index');
        $this->setDefaultController('index');
    }
}
