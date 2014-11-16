<?php
/**
 * Created by PhpStorm.
 * User: quangtm
 * Date: 17/11/2014
 * Time: 00:44
 */

namespace PhalconExt\Mvc;


class Application
{
    public function __construct(array $config)
    {
        xdebug_var_dump($config);
    }

    public function run (){
        echo 'Run project';
    }
}