<?php

namespace Phalex\Mvc;

use Phalex\Mvc\Module\Cache\CacheInterface;
use Phalex\Mvc\Module\AbstractModule;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of Module
 * @todo Autoload class in modules
 * @author quangtm
 */
class Module
{
    /**
     *
     * @var array
     */
    protected $modules;

    /**
     *
     * @var CacheInterface
     */
    protected $cache;

    public function __construct(array $modules, array $paths, CacheInterface $cache = null)
    {
        $this->cache     = $cache;
        $this->modules   = [];
        $isCacheInstance = $this->cache instanceof CacheInterface;
        if ($isCacheInstance) {
            $this->modules = $cache->getRegisteredModules();
        }

        if (empty($this->modules)) {
            $this->setupRegisteredModules($modules, $paths);
            if ($isCacheInstance) {
                $this->cache->setRegisteredModules($this->modules);
            }
        }

        $this->autoloadModuleClasses();
    }

    /**
     * Setup array registered modules when without cache or cache is missed
     * @param array $modules
     * @param array $paths
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    private function setupRegisteredModules($modules, $paths)
    {
        if (empty($modules) || empty($paths)) {
            throw new Exception\InvalidArgumentException('Invalid parameters for init phalcon extesion');
        }
        foreach ($modules as $moduleName) {
            $found = false;
            foreach ($paths as $path) {
                $modulePath = $path . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'Module.php';
                if (file_exists($modulePath)) {
                    $found = true;

                    $this->modules[$moduleName] = [
                        'className' => $moduleName . '\\Module',
                        'path'      => $modulePath,
                    ];
                    break;
                }
            }
            if (!$found) {
                throw new Exception\RuntimeException(sprintf('Not found module "%s"', $moduleName));
            }
        }
    }

    /**
     * Autoload module classes
     */
    private function autoloadModuleClasses()
    {
        foreach ($this->modules as $moduleConfig) {
            if (!class_exists($moduleConfig['className'])) {
                require_once $moduleConfig['path'];
            }
        }
    }

    /**
     * Filter module's configurations
     * @param array $moduleConfig
     * @param string $moduleName
     * @return array
     * @throws Exception\RuntimeException
     */
    protected function filterModuleConfig($moduleConfig, $moduleName)
    {
        if (!ArrayUtils::isHashTable($moduleConfig, true)) {
            $errMsg = sprintf('The configuration for module "%s" is invalid', $moduleName);
            throw new Exception\RuntimeException($errMsg);
        }

        if (isset($moduleConfig['view'])) {
            $realPathView = realpath($moduleConfig['view'][$moduleName]);
            if (!$realPathView) {
                $errMsg = sprintf('The view path for module "%s" is invalid', $moduleName);
                throw new Exception\RuntimeException($errMsg);
            }
            $moduleConfig['view'][$moduleName] = $realPathView;
        }

        return $moduleConfig;
    }

    /**
     * Get autoload config in each module without cache
     * @return array
     * @throws Exception\RuntimeException
     */
    protected function getModulesAutoloadConfigWithoutCache()
    {
        $result = [];
        foreach ($this->modules as $moduleName => $moduleConfig) {
            $className = $moduleConfig['className'];
            $module    = new $className;
            if (!$module instanceof AbstractModule) {
                $errMsg = sprintf('Class "%s" must be extended from %s', $className, AbstractModule::class);
                throw new Exception\RuntimeException($errMsg);
            }
            $autoloadConfig = $module->getAutoloaderConfig();
            if (!ArrayUtils::isHashTable($autoloadConfig)) {
                $errMsg = sprintf('The autoloader configuration for module "%s" is invalid', $moduleName);
                throw new Exception\RuntimeException($errMsg);
            }
            $result = ArrayUtils::merge($result, $autoloadConfig);
        }
        
        foreach ($result as $moduleName => $configAutoload) {
            foreach ($configAutoload as $key => $value) {
                $result[$moduleName][$key] = realpath($value);
            }
        }
        
        return $result;
    }

    /**
     * Get registered modules in phalex
     * @return array
     */
    public function getRegisteredModules()
    {
        return $this->modules;
    }

    /**
     * Get config in modules
     * @return array
     */
    public function getModulesConfig()
    {
        $result = [];
        foreach ($this->modules as $moduleName => $moduleConfig) {
            $className = $moduleConfig['className'];
            $module    = new $className;
            if (!$module instanceof AbstractModule) {
                $errMsg = sprintf('Class "%s" must be extended from %s', $className, AbstractModule::class);
                throw new Exception\RuntimeException($errMsg);
            }
            $config = $module->getConfig();
            $result = ArrayUtils::merge($result, $this->filterModuleConfig($config, $moduleName));
        }
        return $result;
    }

    /**
     * Get all module auto loader configuration
     * @return array
     * @throws Exception\RuntimeException
     */
    public function getModulesAutoloadConfig()
    {
        $isCacheInstance = $this->cache instanceof CacheInterface;
        if ($isCacheInstance) {
            if (($autoloadConfig = $this->cache->getAutoloadModulesConfig()) !== false) {
                return $autoloadConfig;
            }
        }
        
        $autoloadConfig = $this->getModulesAutoloadConfigWithoutCache();
        if ($isCacheInstance) {
            $this->cache->setAutoloadModulesConfig($autoloadConfig);
        }
        return $autoloadConfig;
    }
}
