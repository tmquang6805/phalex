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
    use FileTrait;

    public function __construct(array $options)
    {
        $ds = DIRECTORY_SEPARATOR;

        $this->validateConfig($options);
        $this->fileCache = rtrim($options['dir'], $ds) . $ds . $options['key'] . '_merged_config.dat';
    }
}
