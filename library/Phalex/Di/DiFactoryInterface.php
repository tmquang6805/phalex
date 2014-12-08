<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Di;

/**
 *
 * @author quangtm
 */
interface DiFactoryInterface
{
    public function createService(Di $di);
}
