<?php

/**
 * Created by PhpStorm.
 * User: quangtm
 * Date: 17/11/2014
 * Time: 00:44
 */

namespace Phalcon\Extension\Mvc;

use Phalcon\Extension\Config\Config;
use Phalcon\Extension\Config\Cache as CacheConf;

class Application
{

    public function __construct(array $config)
    {
        $cacheConfigInstance = !isset($config['cache_config']) ? null : $this->getCacheConfig($config['cache_config']);

        /**
         * @todo Autoload register modules
         */
        $init = [
            'modules'           => $this->getModules($config['modules']),
            'config_glob_paths' => $config['config_glob_paths']
        ];

        $config = (new Config($init, $cacheConfigInstance))
                ->getConfig();
    }

    /**
     * 
     * @param array $config
     * @return null|\Phalcon\Extension\Config\Cache\CacheInterface
     */
    private function getCacheConfig(array $config)
    {
        if (!$config['enable']) {
            return null;
        }

        $className = CacheConf::class . '\\' . ucfirst(strtolower($config['adapter']));
        if (!class_exists($className)) {
            throw new Exception\RuntimeException(sprintf('Adapter "%s" is not supported for caching configuration', $config['adapter']));
        }
        return new $className($config['options']);
    }

    /**
     * Init registered module
     * @param array $moduleNames
     * @return array
     */
    private function getModules(array $moduleNames)
    {
        if (empty($moduleNames)) {
            throw new Exception\InvalidArgumentException('Invalid registered modules');
        }

        $modules = [];
        foreach ($moduleNames as $moduleName) {
            $moduleClass = $moduleName . '\\Module';
            $module      = new $moduleClass();
            if ($module instanceof AbstractModule) {
                array_push($modules, $module);
            }
        }
        return $modules;
    }

    public function run()
    {
        echo 'Run project';
    }

}
