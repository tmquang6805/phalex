<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Di\Mock;

use Phalex\Di\DiFactoryInterface;
use Phalex\Di\Di;

/**
 * Description of FactoryMock
 *
 * @author quangtm
 */
class FactoryMock implements DiFactoryInterface
{
    public function createService(Di $di)
    {
        return new DateObject();
    }
}
