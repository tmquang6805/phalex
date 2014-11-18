<?php
/**
 * Created by PhpStorm.
 * User: quangtm
 * Date: 17/11/2014
 * Time: 00:44
 */

namespace PhalconExt\Mvc;

use PhalconExt\Config\Config;

class Application
{
    public function __construct(array $config)
    {
        $config = (new Config($config))
            ->getConfig();
    }

//    private function createAutoloadModulePaths (array $modulePaths)
//    {
//        $result = [];
//        foreach ($modulePaths as $modulePath) {
//            $modulePath = realpath($modulePath);
//            if ($modulePath === false) {
//                throw new \RuntimeException('Invalid module path. Check application config for module autoload paths');
//            }
//            array_push($result, $modulePath);
//        }
//
//        return $result;
//    }

    public function run()
    {
        echo 'Run project';
    }
}
