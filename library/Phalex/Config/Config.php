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
            $configs = ArrayUtils::merge($configs, require $file);
        }

        return $configs;
    }

}
