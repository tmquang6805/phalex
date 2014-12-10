<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Di;

use Phalex\Mvc\Router;
use Phalcon\Config;

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

    /**
     *
     * @var Config
     */
    protected $config;

    public function __construct(Di $di)
    {
        $this->diFactory = $di;
    }

    /**
     *
     * @return Config
     */
    private function getConfig()
    {
        if (!$this->config instanceof Config) {
            $this->config = $this->diFactory->get('config');
        }
        return $this->config;
    }

    /**
     *
     * @return \Phalex\Di\Di
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
        $config = $this->getConfig();
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

    /**
     * Base on config service manager, invokables, create DI service
     *
     * @return \Phalex\Di\DiManager
     * @throws Exception\UnexpectedValueException
     */
    public function initInvokableServices()
    {
        $smConfig = $this->getConfig()['service_manager'];
        if (!isset($smConfig['invokables'])) {
            return $this;
        }
        $invokables = $smConfig['invokables']->toArray();
        $shared     = isset($smConfig['shared']) ? $smConfig['shared']->toArray() : [];
        foreach ($invokables as $serviceName => $serviceClassName) {
            if (!is_string($serviceClassName)) {
                $msg = sprintf('Config for invokable service "%s" must be string data type', $serviceName);
                throw new Exception\UnexpectedValueException($msg);
            }
            $isShare = isset($shared[$serviceName]) ? (bool) $shared[$serviceName] : true;
            $this->diFactory->set($serviceName, $serviceClassName, $isShare);
        }
        return $this;
    }

    protected function setServiceFactories($serviceName, $serviceConfig, $isShare)
    {
        if (!($isCallable = is_callable($serviceConfig)) && !is_string($serviceConfig)) {
            $msg = sprintf('Config for factories service "%s" must be string or callable', $serviceName);
            throw new Exception\UnexpectedValueException($msg);
        }
        
        $this->diFactory->set($serviceName, function () use ($serviceConfig, $isCallable) {
            if ($isCallable) {
                return $serviceConfig($this->diFactory);
            }

            $obj = new $serviceConfig();
            if (!$obj instanceof DiFactoryInterface) {
                $msg = sprintf('Class "%s" must be implemented "%s"', $serviceConfig, DiFactoryInterface::class);
                throw new Exception\RuntimeException($msg);
            }
            return $obj->createService($this->diFactory);
        }, $isShare);
    }

    /**
     * Base on config service manager, factories, create DI services
     *
     * @return \Phalex\Di\DiManager
     * @throws Exception\UnexpectedValueException
     * @throws Exception\RuntimeException
     */
    public function initFactoriedServices()
    {
        $smConfig = $this->getConfig()['service_manager'];
        if (!isset($smConfig['factories'])) {
            return $this;
        }
        $factories = $smConfig['factories']->toArray();
        $shared    = isset($smConfig['shared']) ? $smConfig['shared']->toArray() : [];
        foreach ($factories as $serviceName => $serviceConfig) {
            $isShare = isset($shared[$serviceName]) ? (bool) $shared[$serviceName] : true;
            $this->setServiceFactories($serviceName, $serviceConfig, $isShare);
        }

        return $this;
    }
}
