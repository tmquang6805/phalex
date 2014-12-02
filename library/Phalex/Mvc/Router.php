<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Phalcon\Mvc\Router as PhalconRouter;

/**
 * Description of Router
 *
 * @author quangtm
 */
class Router extends PhalconRouter
{
    public function __construct()
    {
        parent::__construct(false);
        $this->clear();
        $this->removeExtraSlashes(true);
        $this->setUriSource(PhalconRouter::URI_SOURCE_SERVER_REQUEST_URI);
        $this->setDefaultAction('index');
        $this->setDefaultController('index');
    }

    public function addRoute($name, array $routeInfo)
    {
        if (!isset($routeInfo['route']) || !isset($routeInfo['definitions'])) {
            throw new Exception\InvalidArgumentException('Not found required configs for router. Maybe miss "route" or "definitions"');
        }

        $route = $this->add($routeInfo['route'], $routeInfo['definitions']);
        $route->setName($name);

        if (isset($routeInfo['convertions'])) {
            /**
             * @todo Handle setting convertions
             */
        }

        if (isset($routeInfo['before_match'])) {
            /**
             * @todo Handle setting match callback
             */
        }

        if (isset($routeInfo['host_name'])) {
            /**
             * @todo Handle setting match host name
             */
        }
        
        if (isset($routeInfo['methods'])) {
            /**
             * @todo Handle setting match methods
             */
        }
    }
}
