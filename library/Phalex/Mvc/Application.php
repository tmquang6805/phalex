<?php

namespace Phalex\Mvc;

use Phalcon\Mvc\Application as PhalconApplication;
use Phalex\Mvc\Module;
use Phalex\Config\Config as ConfigHandler;
use Phalex\Di;
use Phalex\Loader\Autoloader;
use Phalex\Events\Listener;

class Application
{
    /**
     *
     * @var Di\DiManager
     */
    protected $diManager;

    public function __construct(array $config)
    {
        $cacheModule   = !isset($config['cache_module']) ? null : $this->getCacheInstance($config['cache_module']);
        $cacheConfig   = !isset($config['cache_config']) ? null : $this->getCacheInstance($config['cache_config']);
        $moduleHandler = new Module($config['modules'], $config['autoload_module_paths'], $cacheModule);
        $entireAppConf = (new ConfigHandler($moduleHandler, $config['config_glob_paths'], $cacheConfig))->getConfig();
        $diFactory     = new Di\Di($entireAppConf);
        $diFactory->set('moduleHandler', $moduleHandler, true);

        $this->diManager = new Di\DiManager($diFactory);
        // Create error handler early for handling exception
        $this->setErrorHanlder();
    }

    private function setErrorHanlder()
    {
        $this->diManager->createErrorHandler();
        $errorHandler = $this->diManager->getDI()->get('errorHandler');
        if (!$errorHandler instanceof Exception\HandlerInterface) {
            throw new \RuntimeException(sprintf('%s is invalid', get_class($errorHandler)));
        }

        set_error_handler([$errorHandler, 'errorHandler']);
        set_exception_handler([$errorHandler, 'exceptionHandler']);
    }

    /**
     * Get cache instance for config or module
     * @param array $config
     * @return null|object
     * @throws Exception\RuntimeException
     */
    private function getCacheInstance(array $config)
    {
        if (!isset($config['enable']) || !$config['enable']) {
            return null;
        }

        $className = $config['adapter'];
        if (!class_exists($className)) {
            $errMsg = sprintf('Adapter "%s" is not supported for caching', $config['adapter']);
            throw new Exception\RuntimeException($errMsg);
        }
        return new $className($config['options']);
    }

    public function run()
    {
        $diFactory     = $this->diManager->getDI();
        $moduleHandler = $diFactory->get('moduleHandler');

        // Register autoloader
        (new Autoloader($diFactory))->register();

        // Register services and routers
        $this->diManager->initInvokableServices()
                ->initFactoriedServices()
                ->initRouterDi();

        // Init listeners
        (new Listener($diFactory))
                ->listenApplicationEvents(new Listener\Application())
                ->listenDispatchEvents(new Listener\Dispatch());

        // Register modules
        $application = new PhalconApplication($diFactory);
        $application->setEventsManager($diFactory['eventsManager']);
        $application->registerModules($moduleHandler->getRegisteredModules());

        $application->handle()->send();
    }
}
