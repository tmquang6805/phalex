<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc\Exception;

use Phalex\Di\DiFactoryInterface;

/**
 *
 * @author quangtm
 */
interface HandlerInterface extends DiFactoryInterface
{
    public function exceptionHandler(\Exception $exception);
    public function errorHandler($errorCode, $errorMessage, $errorFile, $errorLine);
}
