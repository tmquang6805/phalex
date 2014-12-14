<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Events\Listener;

use Phalcon\Events\Event;
use Phalex\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine as ViewEngine;

/**
 * Description of Dispatch
 *
 * @author quangtm
 */
class Dispatch
{
    public function beforeDispatchLoop(Event $event, Dispatcher $dispatcher)
    {
    }

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
    }

    public function afterExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
    }

    public function beforeNotFoundAction()
    {
    }

    public function beforeException()
    {
    }

    public function afterDispatchLoop()
    {
    }
}
