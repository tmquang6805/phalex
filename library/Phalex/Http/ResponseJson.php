<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Http;

use Phalcon\Http\Response;

/**
 * Description of ResponseJson
 * @codeCoverageIgnore
 * @author quangtm
 */
class ResponseJson extends Response
{
    public function __construct($content = null)
    {
        parent::__construct();
        $this->setContentType('application/json', 'UTF-8');
        $this->setContent($content);
    }
}
