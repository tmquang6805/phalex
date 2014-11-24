<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc\Module;

use Phalcon\Mvc\ModuleDefinitionInterface;

/**
 * Description of AbstractModule
 *
 * @author quangtm
 */
abstract class AbstractModule implements ModuleDefinitionInterface
{
    abstract public function getConfig();
    abstract public function getAutoloaderConfig();

    final public function registerAutoloaders()
    {
    }

    public function registerServices($dependencyInjector)
    {
    }
}
