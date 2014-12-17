<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc\Module\Cache;

/**
 * Description of File
 *
 * @author quangtm
 */
class File implements CacheInterface
{
    use \Phalex\Config\Cache\FileTrait
    {
        getConfig as protected;
        setConfig as protected;
    }
    
    /**
     *
     * @var string
     */
    protected $registerModulesFile;
    
    /**
     *
     * @var string
     */
    protected $autoloadModulesFile;

    public function __construct(array $options)
    {
        $ds = DIRECTORY_SEPARATOR;

        $this->validateConfig($options);
        $key = rtrim($options['dir'], $ds) . $ds . $options['key'];
        $this->registerModulesFile = $key . '_register_modules.dat';
        $this->autoloadModulesFile = $key . '_autoload.dat';
    }

    public function getRegisteredModules()
    {
        return $this->getConfig($this->registerModulesFile);
    }

    public function setRegisteredModules(array $modules)
    {
        return $this->setConfig($modules, $this->registerModulesFile);
    }

    public function getAutoloadModulesConfig()
    {
        return $this->getConfig($this->autoloadModulesFile);
    }

    public function setAutoloadModulesConfig(array $autoloadConfig)
    {
        return $this->setConfig($autoloadConfig, $this->autoloadModulesFile);
    }
}
