<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc\Router;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Route;

/**
 *
 * @author quangtm
 */
interface BeforeMatchInterface
{
    public function beforeMatch($uri, Route $route, Router $router);
}
