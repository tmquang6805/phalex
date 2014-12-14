<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Events\Listener;

/**
 * Description of Application
 *
 * @author quangtm
 */
class Application
{
    public function boot()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function beforeStartModule()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function afterStartModule()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function beforeHandleRequest()
    {
        xdebug_var_dump(__METHOD__);
    }
    
    public function afterHandleRequest()
    {
        xdebug_var_dump(__METHOD__);
    }
}
