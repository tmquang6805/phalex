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
    protected $modulesConfig;

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

    /**
     *
     * @param array $modulesConfig
     * @param type $globPaths
     * @param \Phalex\Config\Cache\CacheInterface $cache
     */
    public function __construct(array $modulesConfig, $globPaths, Cache\CacheInterface $cache = null)
    {
        $this->modulesConfig = $modulesConfig;
        $this->globPaths     = $globPaths;
        $this->cache         = $cache;
    }

    /**
     * Get entire configurations in application and modules
     *
     * @return array
     */
    public function getConfig()
    {
        //        if ($this->cache instanceof Cache\CacheInterface && ($this->configs = $this->cache->getConfig()) !== false) {
//            return $this->configs;
//        }
//
//        $this->getConfigModules()
//                ->getConfigApp()
//                ->merge()
//                ->setConfigToCache();

        return $this->configs;
    }
}
