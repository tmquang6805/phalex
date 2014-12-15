<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc\Module\Cache;

/**
 *
 * @author quangtm
 */
interface CacheInterface
{
    public function getRegisteredModules();
    public function setRegisteredModules(array $modules);
    public function getAutoloadModulesConfig();
    public function setAutoloadModulesConfig(array $autoloadConfig);
}
