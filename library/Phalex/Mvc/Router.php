<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Phalcon\Mvc\Router as PhalconRouter;
use Phalcon\Mvc\Router\Route;
use Phalcon\DiInterface;
use Phalex\Mvc\Router\ConvertingInterface;

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

    protected function getDiRouteConvert(DiInterface $di, $name, $configConvert)
    {
        if ($di->has($name)) {
            return $di->get($name);
        }

        $di->set($name, function () use ($configConvert) {
            if (is_callable($configConvert)) {
                return $configConvert;
            }
            if (!isset($configConvert['class_name'])) {
                throw new Exception\RuntimeException('Config router convert miss "class_name"');
            }
            $className = $configConvert['class_name'];
            if (!class_exists($className)) {
                throw new Exception\RuntimeException(sprintf('"%s" is not existed', $className));
            }
            
            /**
             * @todo User can config parameter when create new object
             * @todo User can config call method after creating object
             */
            $object = new $className;
            if (!$object instanceof ConvertingInterface) {
                $errMsg = sprintf('"%s" must be implemented "%s"', $className, ConvertingInterface::class);
                throw new Exception\RuntimeException($errMsg);
            }
            return $object;
        }, true);
        
        return $di->get($name);
    }

    protected function setConvertions(Route $route, array $convertions)
    {
        $di = $this->getDI();
        if (!$di instanceof DiInterface) {
            $msg = sprintf('DI must be implemented by "%s" when set convertion', DiInterface::class);
            throw new Exception\UnexpectedValueException($msg);
        }

        foreach ($convertions as $name => $configConvert) {
            $diName     = 'route-di-convertion:' . $name;
            $convertObj = $this->getDiRouteConvert($di, $diName, $configConvert);
            $converter  = is_callable($convertObj) ? $convertObj : [$convertObj, 'convert'];
            $route->convert($name, $converter);
        }
    }

    protected function setHttpMethods(Route $route, array $httpMethods)
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
            $this->setConvertions($route, $routeInfo['convertions']);
        }

        if (isset($routeInfo['before_match'])) {
            /**
             * @todo Handle setting match callback
             */
        }

        if (isset($routeInfo['host_name'])) {
            $route->setHostname($routeInfo['host_name']);
        }

        if (isset($routeInfo['methods'])) {
            $this->setHttpMethods($route, $routeInfo['methods']);
        }
    }
}
