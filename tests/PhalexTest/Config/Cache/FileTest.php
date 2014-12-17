<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhalexTest\Config\Cache;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Config\Cache\File;

/**
 * Description of FileTest
 *
 * @author quangtm
 */
class FileTest extends TestCase
{
    private $folder;
    private $file;


    protected function setUp()
    {
        $this->folder = './tests/data/cache';
        $this->file = $this->folder . DIRECTORY_SEPARATOR . 'test.dat';
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
     * @expectedExceptionMessage Invalid options when create instance Phalex\Config\Cache\File
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
        
        if (file_exists($this->file)) {
            unlink($this->file);
        }
        $this->assertFalse(file_exists($this->file));
        return $fileCache;
    }
    
    /**
     * @depends testConstructorSuccess
     * @param File $cache
     */
    public function testGetConfigFalse(File $cache)
    {
        $this->assertFalse($cache->getConfig());
    }
    
    /**
     * @depends testConstructorSuccess
     * @param File $cache
     */
    public function testSetConfig(File $cache)
    {
        touch($this->file);
        $cache->setConfig(require './tests/config/config.result.php');
        $this->assertTrue(file_exists($this->file));
        return $cache;
    }
    
    /**
     * @depends testSetConfig
     * @param File $cache
     */
    public function testConfigSuccess(File $cache)
    {
        $this->assertEquals(require './tests/config/config.result.php', $cache->getConfig());
    }
    
    /**
     * @depends testSetConfig
     * @param File $cache
     */
    public function testConfigRaiseException(File $cache)
    {
        $errMsg = sprintf('"%s" cannot read', $this->file);
        $this->setExpectedException(\Phalex\Config\Cache\Exception\RuntimeException::class, $errMsg);
        chmod($this->file, 0333);
        $cache->getConfig();
    }
}
