<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Phalcon\Mvc\Router as PhalconRouter;
use Phalcon\Mvc\Router\Route;
use Phalex\Mvc\Router\ConvertingInterface;
use Phalex\Mvc\Router\BeforeMatchInterface;

/**
 * Extends from Phalcon\Mvc\Router
 * In this class default:
 *  Remove extra slashes
 *  Set URI Source is $_SERVER['REQUEST_URI']
 *  Controller and Action is index
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

    /**
     * Base on config for convertion, get callable
     *
     * @param array $config
     * @return object
     * @throws Exception\RuntimeException
     */
    private function getInstanceHelper($config)
    {
        if (is_callable($config)) {
            return $config;
        }
        if (!isset($config['class_name'])) {
            throw new Exception\RuntimeException('Config router convert miss "class_name"');
        }
        $className = $config['class_name'];
        if (!class_exists($className)) {
            throw new Exception\RuntimeException(sprintf('"%s" is not existed', $className));
        }

        /**
         * @todo User can config parameter when create new object
         * @todo User can config call method after creating object
         */
        $object = new $className;

        return $object;
    }

    /**
     * Set convetions callable for route.
     * Convertions allow to freely transform the route’s parameters before passing them to the dispatcher.
     *
     * @param Route $route
     * @param array $convertions
     */
    protected function setConvertions(Route $route, array $convertions)
    {
        foreach ($convertions as $convertionName => $convertConfig) {
            $obj        = $this->getInstanceHelper($convertConfig);
            $isCallable = is_callable($obj);
            if (!$isCallable && !$obj instanceof ConvertingInterface) {
                $errMsg = sprintf('"%s" must be implemented "%s"', get_class($obj), ConvertingInterface::class);
                throw new Exception\RuntimeException($errMsg);
            }

            $converter = $isCallable ? $obj : [$obj, 'convert'];
            $route->convert($convertionName, $converter);
        }
    }

    /**
     * Set before match callable for route.
     * Sometimes, routes must be matched if they meet specific conditions,
     * you can add arbitrary conditions to routes using the ‘beforeMatch’ callback,
     * if this function return false, the route will be treaded as non-matched
     *
     * @param Route $route
     * @param string|callable $config
     */
    protected function setBeforeMatch(Route $route, $config)
    {
        $obj        = $this->getInstanceHelper($config);
        $isCallable = is_callable($obj);
        if (!$isCallable && !$obj instanceof BeforeMatchInterface) {
            $errMsg = sprintf('"%s" must be implemented "%s"', get_class($obj), BeforeMatchInterface::class);
            throw new Exception\RuntimeException($errMsg);
        }

        $beforeMatch = $isCallable ? $obj : [$obj, 'beforeMatch'];
        $route->beforeMatch($beforeMatch);
    }

    /**
     * Set Http methods constraints for router
     *
     * @param Route $route
     * @param array $httpMethods
     */
    protected function setHttpMethods(Route $route, array $httpMethods)
    {
        foreach ($httpMethods as $idx => $method) {
            $httpMethods[$idx] = strtoupper($method);
        }
        $route->via($httpMethods);
    }

    /**
     * Add route by array config
     *
     * @param string $name
     * @param array $routeInfo
     * @throws Exception\InvalidArgumentException
     *
     */
    public function addRoute($name, array $routeInfo)
    {
        if (!isset($routeInfo['route']) || !isset($routeInfo['definitions'])) {
            $errMsg = 'Not found required configs for router. Maybe miss "route" or "definitions"';
            throw new Exception\InvalidArgumentException($errMsg);
        }

        $route = $this->add($routeInfo['route'], $routeInfo['definitions']);
        $route->setName($name);

        if (isset($routeInfo['convertions'])) {
            $this->setConvertions($route, $routeInfo['convertions']);
        }

        if (isset($routeInfo['before_match'])) {
            $this->setBeforeMatch($route, $routeInfo['before_match']);
        }

        if (isset($routeInfo['host_name'])) {
            $route->setHostname($routeInfo['host_name']);
        }

        if (isset($routeInfo['methods'])) {
            $this->setHttpMethods($route, $routeInfo['methods']);
        }
    }
}
