<?php

namespace Phalcon\Extension\Config;

use Phalcon\Extension\Mvc\Module\AbstractModule;
use Phalcon\Config as BaseConf;

class Config
{

    /**
     *
     * @var array
     */
    protected $configs;

    /**
     *
     * @var array
     */
    protected $initParams;

    /**
     *
     * @var Cache\CacheInterface
     */
    protected $cache;

    public function __construct(array $params, Cache\CacheInterface $cache = null)
    {
        if (!$this->isValidConfig($params)) {
            throw new Exception\InvalidArgumentException('Invalid config for Phalcon Extension');
        }

        $this->initParams = $params;
        $this->cache      = $cache;
        $this->configs    = [
            'application' => [],
            'modules'     => [],
        ];
    }

    /**
     * Validate config data for init phalcon extension
     * @param  array   $config
     * @return boolean
     */
    private function isValidConfig(array $config)
    {
        $requiredKeys = [
            'modules'           => 1,
            'config_glob_paths' => 1,
        ];

        if (count($requiredKeys) != count(array_intersect_key($requiredKeys, $config))) {
            return false;
        }

        return true;
    }

    /**
     * Get entire configurations in application and modules
     * 
     * @return array
     */
    public function getConfig()
    {
        if (($this->configs = $this->cache->getConfig()) !== false) {
            return $this->configs;
        }

        $this->getConfigModules()
                ->getConfigApp()
                ->merge()
                ->setConfigToCache();

        return $this->configs;
    }

    private function getConfigModules()
    {
        foreach ($this->initParams['modules'] as $moduleName) {
            $moduleClass = $moduleName . '\\Module';
            $module      = new $moduleClass();
            if ($module instanceof AbstractModule) {
                $this->configs['module'][$moduleName] = new BaseConf($module->getConfig());
            }
        }
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
