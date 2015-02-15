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

        $view = new View($options);
        $this->assertEquals($options['engines'], $view->getRegisteredEngines());
    }

    public function testGetCompiledPath()
    {

        $di      = $this->getMock(Di::class, ['get'], [[]]);
        $viewDir = getcwd() . '/tests/module/Application/view/';

        $view     = new View([
            'di'        => $di,
            'views_dir' => $viewDir,
        ]);
        $viewVolt = '/index/index.volt';

        $this->assertEquals($viewDir . 'index__index.volt.php', $view->getCompiledPath($viewDir . $viewVolt));

        $pathCompiled = getcwd() . '/tests/module/Application/compiled/';
        $compiledFile = $view->getCompiledPath($viewDir . $viewVolt, [
            'path' => $pathCompiled
        ]);
        $this->assertEquals($pathCompiled . 'index__index.volt.php', $compiledFile);

        rmdir($pathCompiled . 'index');
        $compiledFile = $view->getCompiledPath($viewDir . $viewVolt, [
            'path'         => $pathCompiled,
            'hierarchical' => true,
            'extension'    => '.com'
        ]);
        $this->assertEquals($pathCompiled . 'index/index.volt.com', $compiledFile);
        rmdir($pathCompiled . 'index');
    }

    /**
     * @group dev
     */
    public function testGetCompiledPathRaiseException()
    {
        $pathCompiled = getcwd() . '/tests/module/Application/compiled/';
        $folderCompiler = $pathCompiled . 'index';
        $msg= sprintf('Cannot write compile view to "%s"', $folderCompiler);
        $this->setExpectedException(\Phalex\Mvc\Exception\RuntimeException::class, $msg);
        $di      = $this->getMock(Di::class, ['get'], [[]]);
        $viewDir = getcwd() . '/tests/module/Application/view/';

        $view     = new View([
            'di'        => $di,
            'views_dir' => $viewDir,
        ]);
        
        $viewVolt = '/index/index.volt';
        if (file_exists($folderCompiler)) {
            rmdir($folderCompiler);
        }
        mkdir($folderCompiler, 0555);
        
        $view->getCompiledPath($viewDir . $viewVolt, [
            'path'         => $pathCompiled,
            'hierarchical' => true,
            'extension'    => '.com'
        ]);
    }
}
