<?php

namespace Phalex\Config;

use Phalex\Mvc\Module\AbstractModule;
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
    protected $modules;

    /**
     *
     * @var array
     */
    protected $globPaths;

    /**
     *
     * @var Cache\CacheInterface
     */
    protected $cache;

    public function __construct(array $modules, array $globPaths, Cache\CacheInterface $cache = null)
    {
        $this->modules   = $modules;
        $this->globPaths = $globPaths;
        $this->cache     = $cache;
        $this->configs   = [];
    }

    /**
     * Get entire configurations in application and modules
     *
     * @return array
     */
    public function getConfig()
    {
        if ($this->cache instanceof Cache\CacheInterface && ($this->configs = $this->cache->getConfig()) !== false) {
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
        foreach ($this->modules as $moduleName) {
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
