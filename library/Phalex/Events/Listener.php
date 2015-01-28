<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Events;

use Phalex\Di\Di;

/**
 * Description of Listener
 *
 * @author quangtm
 */
class Listener
{
    /**
     *
     * @var Di
     */
    protected $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }
    
    /**
     * Listen application's events
     * @param \Phalex\Events\Listener\Application $listener
     * @return \Phalex\Events\Listener
     */
    public function listenApplicationEvents(Listener\Application $listener)
    {
        $this->di
                ->get('eventsManager')
                ->attach('application', $listener, PHP_INT_MAX);
        return $this;
    }

    /**
     * Listen dispatch's events
     * @param \Phalex\Events\Listener\Dispatch $listener
     * @return \Phalex\Events\Listener
     */
    public function listenDispatchEvents(Listener\Dispatch $listener)
    {
        $this->di
                ->get('eventsManager')
                ->attach('dispatch', $listener, PHP_INT_MAX);
        return $this;
    }
}
