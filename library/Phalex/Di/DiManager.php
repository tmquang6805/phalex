<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Di;

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

//        // Create router instance and set routes for it
//        $router = new Router(false);
//        $router->clear();
//        $router->removeExtraSlashes(true);
//        foreach ($config as $name => $info) {
//            $this->addRoute($router, $name, $info);
//        }
//
//        // Set router into DI
//        $this->diFactory->set('router', $router, true);

        return $this;
    }
}
