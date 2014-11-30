<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Di;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Route;
use Phalex\Router\ConvertingInterface;
use Phalex\Router\BeforeMatchInterface;

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

        // Create router instance and set routes for it
        $router = new Router(false);
        $router->clear();
        $router->removeExtraSlashes(true);
        foreach ($config as $name => $info) {
            $this->setRoute($router, $name, $info);
        }

        // Set router into DI
        $this->diFactory->set('router', $router, true);

        return $this;
    }

    /**
     * Set each route info
     * @param Router $router
     * @param string $name route name
     * @param array $info route info
     * @return \Phalex\Di\DiManager
     * @throws Exception\InvalidArgumentException
     */
    private function setRoute(Router &$router, $name, array $info)
    {
        if (!isset($info['route'])) {
            throw new Exception\InvalidArgumentException('Cannot find "route" info');
        }

        if (!isset($info['definitions'])) {
            throw new Exception\InvalidArgumentException('Cannot find "definitions" info');
        }

        $route = $router->add($info['route'], $info['definitions']);
        $route->setName($name);

        $route = $this->setConvertions($route, $info);
        $route = $this->setMatchCallback($route, $info);
        $route = $this->setHostName($route, $info);
        $route = $this->setMethods($route, $info);
        return $this;
    }

    /**
     * Set DI base on class name
     * @param type $className
     * @throws Exception\RuntimeException
     */
    private function setDiByClassName($className)
    {
        if (!class_exists($className)) {
            throw new Exception\RuntimeException(sprintf('Class "%s" is not found', $className));
        }

        $this->diFactory->set($className, new $className());
    }

    /**
     * Set convertion for route.
     * @link http://docs.phalconphp.com/en/latest/reference/routing.html#using-convertions
     * @param Route $route
     * @param array $info
     * @throws Exception\RuntimeException
     * @return \Phalex\Di\DiManager
     */
    private function setConvertions(Route $route, array $info)
    {
        if (!isset($info['convertions'])) {
            return $route;
        }

        $convertions = $info['convertions'];
        foreach ($convertions as $paramName => $className) {
            if (!$this->diFactory->has($className)) {
                $this->setDiByClassName($className);
            }
            $convert = $this->diFactory->get($className);
            if (!$convert instanceof ConvertingInterface) {
                throw new Exception\RuntimeException(sprintf('Class "%s" is not implemented by "%s" interface', $className, ConvertingInterface::class));
            }
            $route->convert($paramName, [$convert, 'convert']);
        }
        return $route;
    }

    /**
     * Set callback match
     * @link http://docs.phalconphp.com/en/latest/reference/routing.html#match-callbacks
     * @param Route $route
     * @param array $info
     * @return \Phalex\Di\DiManager
     * @throws Exception\RuntimeException
     */
    private function setMatchCallback(Route $route, array $info)
    {
        if (!isset($info['before_match'])) {
            return $route;
        }

        $className = $info['before_match'];
        if (!$this->diFactory->has($className)) {
            $this->setDiByClassName($className);
        }

        $matchCallback = $this->diFactory->get($className);
        if (!$matchCallback instanceof BeforeMatchInterface) {
            throw new Exception\RuntimeException(sprintf('Class "%s" is not implemented by "%s" interface', $className, BeforeMatchInterface::class));
        }
        $route->beforeMatch([$matchCallback, 'beforeMatch']);
        return $route;
    }

    /**
     * Set hostname constraints
     * @link http://docs.phalconphp.com/en/latest/reference/routing.html#hostname-constraints
     * @param Route $route
     * @param array $info
     * @return \Phalex\Di\DiManager
     */
    private function setHostName(Route $route, array $info)
    {
        if (!isset($info['host_name'])) {
            return $route;
        }

        $route->setHostname($info['host_name']);
        return $route;
    }

    /**
     * Set http methods restriction
     * @link http://docs.phalconphp.com/en/latest/reference/routing.html#http-method-restrictions
     * @param Route $route
     * @param array $info
     * @return \Phalex\Di\DiManager
     */
    private function setMethods(Route $route, array $info)
    {
        if (!isset($info['methods'])) {
            return $route;
        }

        $methods = $info['methods'];
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        array_walk($methods, function (&$method)
        {
            $method = strtoupper($method);
        });

        $diff = array_diff($methods, ['POST', 'PUT', 'PATCH', 'GET', 'DELETE', 'OPTIONS', 'HEAD']);
        if (!count($diff)) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid methods: "%s"', implode(' - ', $diff)));
        }

        $route->via($methods);

        return $route;
    }

}
