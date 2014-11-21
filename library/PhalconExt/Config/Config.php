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
    private $modules;

    public function __construct(array $config)
    {
        if (!$this->isValidConfig($config)) {
            throw new Exception\InvalidArgumentException('Invalid config for Phalcon Extension');
        }
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
            'config_glob_paths'     => 1
        ];
        unset($config['cache_config'], $config['cache_module']);
        if (count(array_diff_key($requiredKeys, $config))) {
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
        return false;
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
