<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Exception;
use Phalcon\Mvc\Controller;
use Phalex\Http\ResponseJson;

/**
 * Description of AbstractController
 *
 * @author quangtm
 */
abstract class AbstractController extends Controller
{
    /**
     * @codeCoverageIgnore
     * @param ResponseJson $response
     * @param Exception $exc
     */
    protected function ajaxExceptionCatch(ResponseJson &$response, Exception $exc)
    {
        $response->setContent(json_encode([
            'error' => 1,
            'data'  => [
                'msg'   => 'Has error. Please try again later, thank you!',
                'err'   => $exc->getMessage(),
                'trace' => $exc->getTraceAsString(),
            ],
        ]));
    }
}
