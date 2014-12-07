<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Di;

use Phalex\Mvc\Router;

/**
 * Control setting/getting Phalcon DI
 *
 * @author quangtm
 */
class DiManager
{
    /**
     *
     * @var Di
     */
    protected $diFactory;

    public function __construct(Di $di)
    {
        $this->diFactory = $di;
    }
    
    /**
     *
     * @return Di
     */
    public function getDi()
    {
        return $this->diFactory;
    }

    /**
     * Base on router config, init Di for router
     * @return \Phalex\Di\DiManager
     * @throws Exception\RuntimeException
     */
    public function initRouterDi()
    {
        $config = $this->diFactory->get('config');
        if (!isset($config['router'])) {
            throw new Exception\RuntimeException('Cannot init DI for router. Cannot find router configuration');
        }
        $config = $config['router']->toArray();
        $router = new Router($this->diFactory);
        foreach ($config as $name => $routeInfo) {
            $router->addRoute($name, $routeInfo);
        }

        $this->diFactory->set('router', $router, true);

        return $this;
    }
}
