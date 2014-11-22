<?php

namespace Phalcon\Extension\Mvc;

use Phalcon\Extension\Mvc\Module\Cache\CacheInterface;

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

    public function __construct(array $modules, array $paths, CacheInterface $cache = null)
    {
        if ($cache instanceof CacheInterface) {
            $this->modules = $cache->getRegisteredModules();
            $this->loadCachedModules();
        }
        else {
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

                $this->modules[$moduleName] = [
                    'className' => $moduleName . '\\Module',
                    'path'      => $modulePath,
                ];

                $found = true;
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

}
