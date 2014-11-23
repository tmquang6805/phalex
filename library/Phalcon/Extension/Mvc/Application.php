<?php

/**
 * Created by PhpStorm.
 * User: quangtm
 * Date: 17/11/2014
 * Time: 00:44
 */

namespace Phalcon\Extension\Mvc;

use Phalcon\Events\Manager as EventsManager;
use Phalcon\DI\FactoryDefault;
use Phalcon\Extension\Mvc\Module;
use Phalcon\Extension\Mvc\Module\Cache as CacheModule;
use Phalcon\Extension\Config\Config as ConfigHandler;
use Phalcon\Extension\Config\Cache as CacheConf;

class Application
{

    /**
     * @todo Use later
     * @var EventsManager
     */
    protected $eventsManager;

    public function __construct(array $config)
    {
        $cacheModule       = !isset($config['cache_module']) ? null : $this->getCacheModule($config['cache_module']);
        $moduleHandler     = new Module($config['modules'], $config['autoload_module_paths'], $cacheModule);
        $registeredModules = $moduleHandler->getRegisteredModules();
        $auloadModulesConf = $moduleHandler->getModulesAutoloadConfig();
        $entireAppConf     = $this->getAppConfig($moduleHandler, $config);
    }

    /**
     * Get entire application configuration
     * @param Module $moduleHandler
     * @param array $config
     * @return array
     */
    private function getAppConfig(Module $moduleHandler, $config)
    {
        $cacheConfig   = !isset($config['cache_config']) ? null : $this->getCacheConfig($config['cache_config']);
        $modulesConfig = [];
        if (!$cacheConfig) {
            $modulesConfig = $moduleHandler->getModulesConfig();
        }
//        return (new ConfigHandler($modulesConfig, $config['config_glob_paths'], $cacheConfig))
//                        ->getConfig();
    }

    /**
     * Get cache instance for config or module
     * @param array $config
     * @param string $namespace
     * @param string $errMsg
     * @return null|object
     * @throws Exception\RuntimeException
     */
    private function getCacheInstance(array $config, $namespace, $errMsg)
    {
        if (!isset($config['enable']) || !$config['enable']) {
            return null;
        }

        $className = $namespace . '\\' . ucfirst(strtolower($config['adapter']));
        if (!class_exists($className)) {
            throw new Exception\RuntimeException($errMsg);
        }
        return new $className($config['options']);
    }

    /**
     * 
     * @param array $config
     * @return null|\Phalcon\Extension\Mvc\Module\Cache\CacheInterface
     */
    private function getCacheModule(array $config)
    {
        return $this->getCacheInstance($config, CacheModule::class, sprintf('Adapter "%s" is not supported for caching modules', $config['adapter']));
    }

    /**
     * 
     * @param array $config
     * @return null|\Phalcon\Extension\Config\Cache\CacheInterface
     */
    private function getCacheConfig(array $config)
    {
        return $this->getCacheInstance($config, CacheConf::class, sprintf('Adapter "%s" is not supported for caching configuration', $config['adapter']));
    }

    public function run()
    {
        echo 'Run project';
    }

}
