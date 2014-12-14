<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Events\Listener;

/**
 * Description of Dispatch
 *
 * @author quangtm
 */
class Dispatch
{
    public function beforeDispatchLoop()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function beforeExecuteRoute()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function afterExecuteRoute()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function beforeNotFoundAction()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function beforeException()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function afterDispatchLoop()
    {
        xdebug_var_dump(__METHOD__);
    }
}
