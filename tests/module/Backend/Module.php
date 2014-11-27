<?php

namespace Backend;

use Phalex\Mvc\Module\AbstractModule;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return require __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return require __DIR__ . '/config/autoload.config.php';
    }
}
