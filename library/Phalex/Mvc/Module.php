<?php

namespace Phalex\Mvc;

use Phalex\Mvc\Module\Cache\CacheInterface;
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
        $this->cache = null;
        if ($cache instanceof CacheInterface) {
            $this->modules = $cache->getRegisteredModules();
            $this->loadCachedModules();
            $this->cache   = $cache;
        } else {
            $this->loadModules($modules, array_unique($paths));
        }
    }

    private function loadCachedModules()
    {
        foreach ($this->modules as $moduleInfo) {
            if (!class_exists($moduleInfo['className'])) {
                require_once $moduleInfo['path'];
            }
        }
    }

    /**
     * Detect module class is loaded.
     * If module hasn't been loaded yet, trigger auto load it
     * @param array $modules
     * @param array $paths
     */
    private function loadModules(array $modules, $paths)
    {
        if (empty($modules) || empty($paths)) {
            throw new Exception\InvalidArgumentException('Invalid parameters for init phalcon extesion');
        }
        foreach ($modules as $moduleName) {
            $moduleClass = $moduleName . '\\Module';
            if (!class_exists($moduleClass)) {
                $this->autoloadModule($moduleName, $paths);
            }
        }
    }

    /**
     * Auto load module class
     * @param string $moduleName
     * @param array $paths
     * @throws Exception\RuntimeException
     */
    private function autoloadModule($moduleName, $paths)
    {
        $found = false;
        foreach ($paths as $path) {
            $modulePath = $path . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'Module.php';
            if (file_exists($modulePath)) {
                require_once $modulePath;
                $found = true;

                $this->modules[$moduleName] = [
                    'className' => $moduleName . '\\Module',
                    'path'      => $modulePath,
                ];
                break;
            }
        }
        if (!$found) {
            throw new Exception\RuntimeException(sprintf('Cannot autoload module "%s"', $moduleName));
        }
    }

    /**
     * Get registered modules for phalcon application
     * @return array
     */
    public function getRegisteredModules()
    {
        return $this->modules;
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
            throw new Exception\RuntimeException(sprintf('The configuration for module "%s" is invalid', $moduleName));
        }

        foreach ($moduleConfig as $config) {
            $realPathView = realpath($config['view']);
            if (!$realPathView) {
                throw new Exception\RuntimeException(sprintf('The view path for module "%s" is invalid', $moduleName));
            }
            $moduleConfig[$moduleName]['view'] = $realPathView;
        }

        return $moduleConfig;
    }

    protected function setModuleClasses()
    {
        $result = [];
        foreach ($this->modules as $moduleName => $module) {
            $result[$moduleName] = new $module['className'];
        }
        return $result;
    }

    /**
     * Get all module configurations
     * @return array
     * @throws Exception\RuntimeException
     */
    public function getModulesConfig()
    {
        $result = [];
        foreach ($this->setModuleClasses() as $moduleName => $module) {
            $config = $module->getConfig();
            $result = ArrayUtils::merge($result, $this->filterModuleConfig($config, $moduleName));
        }
        return $result;
    }

    /**
     *
     * @param array $autoloadConfig
     * @return array
     */
    private function getRealPathAutoloadConfig($autoloadConfig)
    {
        foreach ($autoloadConfig as $moduleName => $configAutoload) {
            foreach ($configAutoload as $key => $value) {
                $autoloadConfig[$moduleName][$key] = realpath($value);
            }
        }
        return $autoloadConfig;
    }

    /**
     * Get all module auto loader configuration
     * @return array
     * @throws Exception\RuntimeException
     */
    public function getModulesAutoloadConfig()
    {
        if ($this->cache) {
            return $this->cache->getAutoloadModulesConfig();
        }

        $result = [];
        foreach ($this->setModuleClasses() as $moduleName => $module) {
            $autoloadConfig = $module->getAutoloaderConfig();
            if (!ArrayUtils::isHashTable($autoloadConfig)) {
                throw new Exception\RuntimeException(sprintf('The autoloader configuration for module "%s" is invalid', $moduleName));
            }
            $result = ArrayUtils::merge($result, $autoloadConfig);
        }
        $result = $this->getRealPathAutoloadConfig($result);
        return $result;
    }
}
