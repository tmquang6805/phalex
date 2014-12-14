<?php

/**
 * Created by PhpStorm.
 * User: quangtm
 * Date: 17/11/2014
 * Time: 00:44
 */

namespace Phalex\Mvc;

use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Application as PhalconApplication;
use Phalex\Mvc\Module;
use Phalex\Mvc\Module\Cache as CacheModule;
use Phalex\Config\Config as ConfigHandler;
use Phalex\Config\Cache as CacheConf;
use Phalex\Di;
use Phalex\Loader\Autoloader;

class Application
{
    /**
     * @todo Use later
     * @var EventsManager
     */
    protected $eventsManager;

    /**
     *
     * @var Di\DiManager
     */
    protected $diManager;

    public function __construct(array $config)
    {
        $cacheModule   = !isset($config['cache_module']) ? null : $this->getCacheModule($config['cache_module']);
        $moduleHandler = new Module($config['modules'], $config['autoload_module_paths'], $cacheModule);
        $entireAppConf = $this->getAppConfig($moduleHandler, $config);
        $diFactory     = new Di\Di($entireAppConf);

        $this->diManager = new Di\DiManager($diFactory);
        $diFactory->set('moduleHandler', $moduleHandler, true);
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
        return (new ConfigHandler($modulesConfig, $config['config_glob_paths'], $cacheConfig))
                        ->getConfig();
    }

    /**
     * Get cache instance for config or module
     * @param array $config
     * @param string $namespace
     * @return null|object
     * @throws Exception\RuntimeException
     */
    private function getCacheInstance(array $config, $namespace)
    {
        if (!isset($config['enable']) || !$config['enable']) {
            return null;
        }

        $className = $namespace . '\\' . ucfirst(strtolower($config['adapter']));
        if (!class_exists($className)) {
            $errMsg = sprintf('Adapter "%s" is not supported for caching', $config['adapter']);
            throw new Exception\RuntimeException($errMsg);
        }
        return new $className($config['options']);
    }

    /**
     *
     * @param array $config
     * @return null|\Phalex\Mvc\Module\Cache\CacheInterface
     */
    private function getCacheModule(array $config)
    {
        return $this->getCacheInstance($config, CacheModule::class);
    }

    /**
     *
     * @param array $config
     * @return null|\Phalex\Config\Cache\CacheInterface
     */
    private function getCacheConfig(array $config)
    {
        return $this->getCacheInstance($config, CacheConf::class);
    }

    public function run()
    {
        try {
            $diFactory     = $this->diManager->getDi();
            $moduleHandler = $diFactory->get('moduleHandler');

            // Register autoloader
            (new Autoloader($diFactory))->register();

            // Register services and routers
            $this->diManager->initInvokableServices()
                    ->initFactoriedServices()
                    ->initRouterDi();

            // Register modules
            $application = new PhalconApplication($diFactory);
            $application->setEventsManager($diFactory['eventsManager']);
            $application->registerModules($moduleHandler->getRegisteredModules());
            
//            $application->handle()->send();
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }
    }
}
