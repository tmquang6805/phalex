<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Router;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Route;
use Phalex\Mvc\Router\BeforeMatchInterface;

/**
 * Description of BeforeMatchMock
 *
 * @author quangtm
 */
class BeforeMatchMock implements BeforeMatchInterface
{
    public function beforeMatch($uri, Route $route, Router $router)
    {
        return true;
    }
}
