<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Mvc\Exception;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Mvc\Exception\HandlerDefault;
use Phalex\Mvc\Exception\HandlerInterface;
use Phalex\Di\Di;
use Phalcon\Config;

/**
 * Description of HandlerDefaultTest
 *
 * @author quangtm
 */
class HandlerDefaultTest extends TestCase
{
    public function supplyTestCreateServiceRaiseException()
    {
        return [
            [
                [
                    'error_handler' => [],
                ],
            ],
            [
                [
                    'error_handler' => [
                        'options' => [
                            'template_500' => 'error.phtml',
                            'template_404' => 'not-found.phtml'
                        ],
                    ],
                ],
            ],
            [
                [
                    'error_handler' => [
                        'options' => [
                            'views_dir'    => 'not-real-path',
                            'template_404' => 'not-found.phtml'
                        ],
                    ],
                ],
            ],
            [
                [
                    'error_handler' => [
                        'options' => [
                            'views_dir'    => 'not-real-path',
                            'template_500' => 'error.phtml',
                        ],
                    ],
                ],
            ],
            [
                [
                    'error_handler' => [
                        'options' => [
                            'views_dir'    => 'not-real-path',
                            'template_500' => 'error.phtml',
                            'template_404' => 'not-found.phtml'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider supplyTestCreateServiceRaiseException
     * @param type $config
     * @expectedException \Phalex\Mvc\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot create error handler "Phalex\Mvc\Exception\HandlerDefault"
     */
    public function testCreateServiceRaiseException($config)
    {
        $diMock = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        (new HandlerDefault())->createService($diMock);
    }

    /**
     * @group dev
     * @return type
     */
    public function testCreateServiceSuccess()
    {
        $config  = [
            'error_handler' => [
                'options' => [
                    'views_dir'    => './tests/module/Application/view/error',
                    'template_500' => 'error.phtml',
                    'template_404' => 'not-found.phtml'
                ],
            ],
        ];
        $diMock  = $this->getMockBuilder(Di::class)
                ->disableOriginalConstructor()
                ->getMock();
        $diMock->expects($this->once())
                ->method('get')
                ->with('config')
                ->will($this->returnValue(new Config($config)));
        $service = (new HandlerDefault())->createService($diMock);
        $this->assertInstanceOf(HandlerDefault::class, $service);
        $this->assertInstanceOf(HandlerInterface::class, $service);
        return $service;
    }

    public function testHandlerError()
    {
        $handlerDefaultMock = $this->getMock(HandlerDefault::class);
        $handlerDefaultMock->expects($this->once())
                ->method('errorHandler');
        set_error_handler([$handlerDefaultMock, 'errorHandler']);
        trigger_error('test error');
    }
}
