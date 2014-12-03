<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc\Router;

use Phalcon\DI as PhalconDi;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of Convertions
 *
 * @author quangtm
 */
class Convertions extends PhalconDi
{
    /**
     *
     * @var PhalconDi
     */
    protected $applicationDi;

    public function __construct(PhalconDi $appDi, array $convertions = [])
    {
        $this->applicationDi = $appDi;
        if (!ArrayUtils::isHashTable($convertions, true)) {
            throw new Exception\InvalidArgumentException('Cannot create router convertions. Config must be hash table');
        }

        foreach ($convertions as $name => $defintion) {
            $this->set($name, $defintion);
        }

        parent::__construct();
    }

    public function set($name, $definition, $shared = null)
    {
        $className = $definition['class_name'];
        $options   = isset($definition['options']) ? $definition['options'] : [];

        if (!class_exists($className)) {
            throw new Exception\RuntimeException(sprintf('Class name "%s" is not existed', $className));
        }

        if ($this->applicationDi->has($className)) {
            $convert = $this->applicationDi->get($className);
        } else {
            $convert = call_user_func_array([$className, '__construct'], $options);
        }
        
        if (!$convert instanceof ConvertingInterface) {
            $errMsg = sprintf('Class "%s" must be instance of "%s" interface', $className, ConvertingInterface::class);
            throw new Exception\RuntimeException($errMsg);
        }

        parent::set($name, $definition, $shared);
    }

//    public function set($name, $className, $shared = null)
//    {
//        if (!class_exists($className)) {
//
//        }
//
//        if ($this->applicationDi->has($className)) {
//            $convert = $this->applicationDi->get($className);
//        }
//
//
//
//        $convert = new $className();
//    }
}
