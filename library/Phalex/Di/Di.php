<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Di;

use Phalcon\DI\FactoryDefault;
use Phalcon\Config;

/**
 * Control setting/getting Phalcon DI
 *
 * @author quangtm
 */
class Di extends FactoryDefault
{
    public function __construct(array $entireAppConfig)
    {
        parent::__construct();
        $this->set('config', new Config($entireAppConfig), true);
    }
}
