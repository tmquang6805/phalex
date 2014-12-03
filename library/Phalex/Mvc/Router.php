<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Phalcon\Mvc\Router as PhalconRouter;
use Phalcon\Mvc\Router\Route;

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

    private function setHttpMethods(Route $route, array $httpMethods)
    {
        foreach ($httpMethods as $idx => $method) {
            $httpMethods[$idx] = strtoupper($method);
        }
        $route->via($httpMethods);
    }

    public function addRoute($name, array $routeInfo)
    {
        if (!isset($routeInfo['route']) || !isset($routeInfo['definitions'])) {
            $errMsg = 'Not found required configs for router. Maybe miss "route" or "definitions"';
            throw new Exception\InvalidArgumentException($errMsg);
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
            $this->setHttpMethods($route, $routeInfo['methods']);
        }
    }
}
