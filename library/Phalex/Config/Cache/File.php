<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Config\Cache;

/**
 * Description of File
 *
 * @author quangtm
 */
class File implements CacheInterface
{
    public function __construct($options)
    {
    }

    public function getConfig()
    {
        return false;
    }
}
