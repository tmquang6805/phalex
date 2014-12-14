<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Config\Cache;

/**
 *
 * @author quangtm
 */
interface CacheInterface
{
    public function getConfig();
    public function setConfig(array $config);
}
