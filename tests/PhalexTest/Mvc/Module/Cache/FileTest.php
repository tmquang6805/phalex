<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Mvc\Module\Cache;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Mvc\Module\Cache\File;

/**
 * Description of FileTest
 *
 * @author quangtm
 */
class FileTest extends TestCase
{
    private $folder;
    private $fileRegisterModule;
    private $fileAutoloadModule;

    protected function setUp()
    {
        $this->folder = './tests/data/cache';
        if (!is_dir($this->folder)) {
            mkdir($this->folder, 0755, true);
        }
        $this->fileRegisterModule = $this->folder . DIRECTORY_SEPARATOR . 'test_register_modules.dat';
        $this->fileAutoloadModule = $this->folder . DIRECTORY_SEPARATOR . 'test_autoload.dat';
    }
    
    public function supplyTestConstructorRaiseExceptionInvalidOptions()
    {
        return [
            [
                []
            ],
            [
                ['key' => '']
            ],
            [
                ['dir' => '']
            ],
            [
                [
                    'key' => null,
                    'dir' => null,
                ]
            ],
        ];
    }
    /**
     * @expectedException Phalex\Config\Cache\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid options when create instance Phalex\Mvc\Module\Cache\File
     * @dataProvider supplyTestConstructorRaiseExceptionInvalidOptions
     */
    public function testConstructorRaiseExceptionInvalidOptions($data)
    {
        new File($data);
    }
    
    public function supplyTestConstructorUnexpectedKey()
    {
        return [
            [
                [
                    'key' => '',
                    'dir' => '',
                ]
            ],
            [
                [
                    'key' => [],
                    'dir' => '',
                ]
            ],
            [
                [
                    'key' => new \ArrayObject(),
                    'dir' => '',
                ]
            ],
        ];
    }
    
    /**
     * @expectedException Phalex\Config\Cache\Exception\UnexpectedValueException
     * @expectedExceptionMessage The "key" config must be string data type
     * @dataProvider supplyTestConstructorUnexpectedKey
     */
    public function testConstructorUnexpectedKey($data)
    {
        new File($data);
    }
    
    /**
     * @expectedException Phalex\Config\Cache\Exception\UnexpectedValueException
     * @expectedExceptionMessage The "dir" config must be writable folder
     */
    public function testConstructorUnwritableDir()
    {
        new File([
            'key' => 'test',
            'dir' => 'not_exists',
        ]);
    }
    
    public function testConstructorSuccess()
    {
        if (!is_writable($this->folder)) {
            chmod($this->folder, 0777);
        }
        $this->assertTrue(is_writable($this->folder));
        $fileCache = new File([
            'key' => 'test',
            'dir' => $this->folder
        ]);
        
        if (file_exists($this->fileRegisterModule)) {
            unlink($this->fileRegisterModule);
        }
        
        if (file_exists($this->fileAutoloadModule)) {
            unlink($this->fileAutoloadModule);
        }
        $this->assertFalse(file_exists($this->fileRegisterModule));
        $this->assertFalse(file_exists($this->fileAutoloadModule));
        return $fileCache;
    }
    
    /**
     * @depends testConstructorSuccess
     * @param File $cache
     */
    public function testGetRegisteredModulesFalse(File $cache)
    {
        $this->assertFalse($cache->getRegisteredModules());
    }
    
    /**
     * @depends testConstructorSuccess
     * @param File $cache
     */
    public function testGetAutoloadModulesFalse(File $cache)
    {
        $this->assertFalse($cache->getAutoloadModulesConfig());
    }
    
    /**
     * @depends testConstructorSuccess
     * @param File $cache
     */
    public function testSetRegisteredModules(File $cache)
    {
        touch($this->fileRegisterModule);
        $cache->setRegisteredModules(require './tests/config/module_register.result.php');
        $this->assertTrue(file_exists($this->fileRegisterModule));
        return $cache;
    }
    
      
    /**
     * @depends testSetRegisteredModules
     * @param File $cache
     */
    public function testSetRegisterSuccess(File $cache)
    {
        $this->assertEquals(require './tests/config/module_register.result.php', $cache->getRegisteredModules());
    }
    
    /**
     * @depends testConstructorSuccess
     * @param File $cache
     */
    public function testSetAutoloadModules(File $cache)
    {
        touch($this->fileRegisterModule);
        $cache->setAutoloadModulesConfig(require './tests/config/autoload.result.php');
        $this->assertTrue(file_exists($this->fileRegisterModule));
        return $cache;
    }
    
    /**
     * @depends testSetAutoloadModules
     * @param File $cache
     */
    public function testSetAutoloadSuccess(File $cache)
    {
        $this->assertEquals(require './tests/config/autoload.result.php', $cache->getAutoloadModulesConfig());
    }
}
