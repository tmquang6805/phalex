<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Events\Listener;

use Phalcon\Events\Event;
use Phalcon\Mvc\Application as PhalconApp;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Config;
use Phalcon\Mvc\Url as UrlService;
use Phalex\Mvc\View;
use Phalex\Events\Exception;
use Phalex\Di\Di;

/**
 * Description of Application
 *
 * @author quangtm
 */
class Application
{
    public function boot(Event $event, PhalconApp $app)
    {
        $router = $app->router;
        $router->handle($router->getRewriteUri());
        if (!$router->wasMatched()) {
            throw new DispatchException('Cannot match route');
        }
        $app->getDI()->set('matchedRoute', $router->getMatchedRoute());
    }

    private function setVoltOptions(Config $config, $moduleName)
    {
        $volt = isset($config['volt'][$moduleName]) ? $config['volt'][$moduleName]->toArray() : [];
        if (isset($volt['path'])) {
            $volt['path'] = realpath($volt['path']);
            if ($volt['path'] === false) {
                throw new Exception\RuntimeException('Not found compiled folder for volt engine');
            }
            $volt['path'] .= DIRECTORY_SEPARATOR;
            if (!is_writable($volt['path'])) {
                throw new Exception\RuntimeException('Compiled folder is not writable');
            }
        }
        return $volt;
    }

    /**
     * Set service view for application when module start
     * @param Di $di
     * @param Config $config
     * @param string $moduleName
     * @return \Phalex\Events\Listener\Application
     */
    private function setViewService(Di $di, Config $config, $moduleName)
    {
        $viewsDir = $config['view'][$moduleName];
        $volt     = $this->setVoltOptions($config, $moduleName);

        $options = [
            'di'        => $di,
            'views_dir' => $viewsDir,
            'volt'      => $volt
        ];
        $di->set('view', new View($options), true);
        return $this;
    }

    /**
     * Set url service when module start
     * @param Di $di
     * @param Config $config
     * @param string $name Module name
     * @return \Phalex\Events\Listener\Application
     */
    private function setUrlService(Di $di, Config $config, $name)
    {
        $base   = $static = '/';
        if (isset($config['url'])) {
            $default = isset($config['url']['default']) ? $config['url']['default'] : '/';
            if (isset($config['url'][$name])) {
                $base   = isset($config['url'][$name]['uri']) ? $config['url'][$name]['uri'] : $default;
                $static = isset($config['url'][$name]['static']) ? $config['url'][$name]['static'] : $default;
            }
        }
        
        $url = new UrlService();
        $url->setBaseUri($base);
        $url->setStaticBaseUri($static);
        $di->set('url', $url, true);
        return $this;
    }

    public function beforeStartModule(Event $event, PhalconApp $app, $moduleName)
    {
        $di     = $app->getDI();
        $config = $di->get('config');
        $this->setViewService($di, $config, $moduleName)
                ->setUrlService($di, $config, $moduleName);
    }
}
