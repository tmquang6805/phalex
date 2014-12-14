<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Events\Listener;

use Phalcon\Events\Event;
use Phalcon\Mvc\Application as PhalconApp;
use Phalex\Mvc\View;

/**
 * Description of Application
 *
 * @author quangtm
 */
class Application
{
    public function boot()
    {
        //xdebug_var_dump(__METHOD__);
    }

    public function beforeStartModule(Event $event, PhalconApp $app, $moduleName)
    {
        $di       = $app->getDI();
        $viewsDir = $di->get('config')['view'][$moduleName];
        $options  = [
            'di'        => $di,
            'views_dir' => $viewsDir,
        ];
        $di->set('view', new View($options), true);
    }

    public function afterStartModule(Event $event, PhalconApp $app, $moduleName)
    {
        //        xdebug_var_dump(__METHOD__, $moduleName);
    }

    public function beforeHandleRequest()
    {
        //        xdebug_var_dump(__METHOD__);
    }

    public function afterHandleRequest()
    {
        //        xdebug_var_dump(__METHOD__);
    }
}
