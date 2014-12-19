<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Events\Listener;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Text;
use Phalex\Mvc\Dispatcher;

/**
 * Description of Dispatch
 *
 * @author quangtm
 */
class Dispatch
{
    /**
     *
     * @param Dispatcher $dispatcher
     * @example http://example.com/controller/key1/value1/key2/value key1 = value1, key2 = value2
     * @link http://docs.phalconphp.com/en/latest/reference/dispatching.html#preparing-parameters
     * @return \Phalex\Events\Listener\Dispatch
     */
    private function prepareParameters(Dispatcher $dispatcher)
    {
        $keyParams = array();
        $params    = $dispatcher->getParams();
        ksort($params, SORT_NATURAL);

        //Use odd parameters as keys and even as values
        foreach ($params as $number => $value) {
            if ($number & 1) {
                $keyParams[$params[$number - 1]] = $value;
                unset($params[$number - 1], $params[$number]);
            }
        }
        
        //Override parameters
        $dispatcher->setParams($keyParams);
        return $this;
    }

    
    /**
     * Camelize action name
     * @param Dispatcher $dispatcher
     * @param string $actionName
     * @link http://docs.phalconphp.com/en/latest/reference/dispatching.html#camelize-action-names
     * @return \Phalex\Events\Listener\Dispatch
     */
    private function camelize(Dispatcher $dispatcher, $actionName)
    {
        $dispatcher->setActionName(lcfirst(Text::camelize($actionName)));
        return $this;
    }

    public function beforeDispatchLoop(Event $event, Dispatcher $dispatcher)
    {
        $controllerName = $dispatcher->getControllerName();
        $actionName     = $dispatcher->getActionName();

        if ($controllerName == 'error' && $actionName == 'not-found') {
            throw new DispatchException('Cannot match route');
        }
        
        $this->camelize($dispatcher, $actionName)
            ->prepareParameters($dispatcher);
    }

    private function pickView(Dispatcher $dispatcher)
    {
        $func = function ($e) {
            return '-' . strtolower($e[0]);
        };

        $controller     = $dispatcher->getActiveController();
        $controllerName = preg_replace_callback('/[A-Z]/', $func, lcfirst($dispatcher->getControllerName()));
        $actionName     = preg_replace_callback('/[A-Z]/', $func, lcfirst($dispatcher->getActionName()));
        $controller->view->pick($controllerName . DIRECTORY_SEPARATOR . $actionName);

        return $this;
    }

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $this->pickView($dispatcher);
    }
}
