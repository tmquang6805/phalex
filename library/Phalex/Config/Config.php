<?php

namespace Phalex\Config;

use Zend\Stdlib\ArrayUtils;

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
    protected $files;

    /**
     *
     * @var Cache\CacheInterface
     */
    protected $cache;

    /**
     *
     * @param array $modulesConfig
     * @param string|array $globPaths
     * @param \Phalex\Config\Cache\CacheInterface $cache
     */
    public function __construct(array $modulesConfig, $globPaths, Cache\CacheInterface $cache = null)
    {
        $this->modulesConfig = $modulesConfig;
        $this->cache         = $cache;

        $this->files = [];
        if (!is_array($globPaths)) {
            $globPaths = [$globPaths];
        }

        foreach ($globPaths as $globPath) {
            foreach (glob($globPath, GLOB_BRACE) as $file) {
                array_push($this->files, $file);
            }
        }
    }

    /**
     * Get entire configurations in application and modules
     *
     * @return array
     */
    public function getConfig()
    {
        if ($this->cache instanceof Cache\CacheInterface && !empty($configs = $this->cache->getConfig())) {
            return $configs;
        }

        $configs = $this->modulesConfig;
        foreach ($this->files as $file) {
            $config = require $file;
            if (!is_array($config)) {
                throw new Exception\RuntimeException(sprintf('The config in "%s" file must be array data type', $file));
            }
            $configs = ArrayUtils::merge($configs, require $file);
        }
        
        return $this->cleanUp($configs);
    }

    /**
     * Get real path for view path configs
     * @param array $configViewPaths
     * @return array
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    protected function filterViewPath($configViewPaths)
    {
        if (!ArrayUtils::isHashTable($configViewPaths)) {
            throw new Exception\InvalidArgumentException('Config view path is not valid');
        }

        foreach ($configViewPaths as $namespace => $viewPath) {
            $viewPath = realpath($viewPath);
            if (!$viewPath) {
                $errMsg = sprintf('Config view path for "%s" module is invalid', $namespace);
                throw new Exception\RuntimeException($errMsg);
            }
            $configViewPaths[$namespace] = $viewPath;
        }
        return $configViewPaths;
    }

    /**
     * Clean up entire application config
     * @param array $configs
     * @return array
     */
    protected function cleanUp(array $configs)
    {
        $configs['view'] = !isset($configs['view']) ? [] : $this->filterViewPath($configs['view']);
        return $configs;
    }
}
