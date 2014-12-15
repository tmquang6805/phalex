<?php

namespace WrongModuleConfig;

use Phalex\Mvc\Module\AbstractModule;

class Module extends AbstractModule
{
    public function getAutoloaderConfig()
    {
        return [1, 2, 3];
    }

    public function getConfig()
    {
        return [1, 2, 3];
    }
}
