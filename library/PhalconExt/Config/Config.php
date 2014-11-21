<?php

/**
 * Created by PhpStorm.
 * User: quangtm
 * Date: 18/11/2014
 * Time: 00:10
 */

namespace PhalconExt\Config;

class Config
{
    /**
     *
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        if (!$this->isValidConfig($config)) {
            throw new Exception\InvalidArgumentException('Invalid config for Phalcon Extension');
        }

        $this->config = $config;
    }

    /**
     * Validate config data for init phalcon extension
     * @param  array   $config
     * @return boolean
     */
    private function isValidConfig(array $config)
    {
        $requiredKeys = [
            'modules'               => 1,
            'autoload_module_paths' => 1,
            'config_glob_paths'     => 1,
        ];

        if (count($requiredKeys) != count(array_intersect_key($requiredKeys, $config))) {
            return false;
        }

        return true;
    }

    public function getConfig()
    {
        if (($this->config = $this->getConfigFromCache()) !== false) {
            return $this->config;
        }

        $this->getConfigModules()
                ->getConfigApp()
                ->merge()
                ->setConfigToCache();

        return $this->config;
    }

    private function getConfigFromCache()
    {
        if (!isset($this->config['cache_config'])) {
            return false;
        }

        $cacheConfig = $this->config['cache_config'];
        if (!isset($cacheConfig['enable']) || !$cacheConfig['enable']) {
            return false;
        }

        $className = __NAMESPACE__ . '\\Cache\\' . ucfirst(strtolower($cacheConfig['adapter']));
        if (!class_exists($className)) {
            throw new Exception\RuntimeException("Adapter for caching config is not support. Please check it again");
        }

        return (new $className($cacheConfig['options']))
                        ->getConfig();
    }

    private function getConfigModules()
    {
        return $this;
    }

    private function getConfigApp()
    {
        return $this;
    }

    private function merge()
    {
        return $this;
    }

    private function setConfigToCache()
    {
        return $this;
    }
}
