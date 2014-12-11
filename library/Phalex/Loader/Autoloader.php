<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Loader;

use Phalcon\Loader;
use Phalcon\Events\Manager as EventsManager;
use Phalex\Di\Di;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of Autoloader
 *
 * @author quangtm
 */
class Autoloader
{
    /**
     *
     * @var Di
     */
    protected $diFactory;

    /**
     *
     * @var Loader
     */
    protected $loader;

    /**
     *
     * @param Di $diFactory
     */
    public function __construct(Di $diFactory)
    {
        $this->diFactory = $diFactory;
        $this->loader    = new Loader();

        $ev = new EventsManager();
        $ev->enablePriorities(true);
        $ev->collectResponses(true);

        $diFactory->set('autoloaderEventsManager', $ev, true);
        $this->loader->setEventsManager($ev);
    }

    /**
     * Register class map
     * @param array $classMap
     * @throws Exception\RuntimeException
     * @todo Should cache multi include file into one
     */
    protected function registerClassMap(array $classMap)
    {
        foreach ($classMap as $file) {
            $arrClasses = require $file;
            if (!ArrayUtils::isHashTable($arrClasses, true)) {
                throw new Exception\RuntimeException('Config autoload for classmap is invalid');
            }

            if (!empty($arrClasses)) {
                $this->loader->registerClasses($arrClasses);
            }
        }
    }

    /**
     * Auto register namespace and class map
     * @throws Exception\RuntimeException
     * @todo Should cache register classmap
     */
    public function register()
    {
        $moduleHandler = $this->diFactory->get('moduleHandler');
        $autoloadConf  = $moduleHandler->getModulesAutoloadConfig();

        if (isset($autoloadConf['namespaces'])) {
            if (!ArrayUtils::isHashTable($autoloadConf['namespaces'])) {
                throw new Exception\RuntimeException('Config autoload for namespace is invalid');
            }
            $this->loader->registerNamespaces($autoloadConf['namespaces']);
        }

        if (isset($autoloadConf['classmap'])) {
            $this->registerClassMap($autoloadConf['classmap']);
        }
        $this->loader->register();
        return $this;
    }
    
    public function unregister()
    {
        $this->loader->unregister();
        return $this;
    }
}
