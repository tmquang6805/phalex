<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Mvc\View;
use Phalex\Di\Di;
use Phalcon\Mvc\View\Engine;

/**
 * Description of ViewTest
 *
 * @author quangtm
 */
class ViewTest extends TestCase
{
    public function supplyTestConstructorRaiseException()
    {
        $di = $this->getMock(Di::class, ['get', 'set'], [[]]);
        return [
            [
                []
            ],
            [
                [
                    'di'        => new \stdClass(),
                    'views_dir' => '.',
                ]
            ],
            [
                [
                    'di' => $di,
                ]
            ]
        ];
    }

    /**
     * @expectedException Phalex\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid options for Phalex\Mvc\View
     * @dataProvider supplyTestConstructorRaiseException
     */
    public function testConstructorRaiseException($data)
    {
        new View($data);
    }

    public function testConstructorDefault()
    {
        $di = $this->getMock(Di::class, ['get'], [[]]);

        $view    = new View([
            'di'        => $di,
            'views_dir' => '.',
        ]);
        $engines = [
            '.phtml',
            '.volt',
        ];
        $this->assertEquals($engines, array_keys($view->getRegisteredEngines()));
    }

    public function testConstructorSetEngines()
    {
        $di = $this->getMock(Di::class, ['get'], [[]]);
        
        $options = [
            'di'        => $di,
            'views_dir' => '.',
            'engines'   => [
                '.phtml' => Engine\Php::class,
            ]
        ];
        $view    = new View($options);
        $this->assertEquals($options['engines'], $view->getRegisteredEngines());
    }
}
