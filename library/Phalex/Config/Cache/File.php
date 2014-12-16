<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Config\Cache;

use Zend\Config\Writer\PhpArray;

/**
 * Description of File
 *
 * @author quangtm
 */
class File implements CacheInterface
{
    /**
     *
     * @var string
     */
    private $fileCache;

    public function __construct(array $options)
    {
        if (!isset($options['key']) || !isset($options['dir'])) {
            throw new Exception\InvalidArgumentException('Invalid options when create instance cache');
        }

        if (!is_string($options['key'])) {
            throw new Exception\UnexpectedValueException('The "key" config must be string data type');
        }

        if (!is_dir($options['dir']) || !is_writable($options['dir'])) {
            throw new Exception\UnexpectedValueException('The "dir" config must be writable folder');
        }

        $ds              = DIRECTORY_SEPARATOR;
        $this->fileCache = rtrim($options['dir'], $ds) . $ds . $options['key'] . '.dat';
    }

    /**
     * Get config from file cache
     * @return array|false Return array config when file existed and readable, otherwise return false
     * @throws Exception\RuntimeException
     */
    public function getConfig()
    {
        if (file_exists($this->fileCache)) {
            if (!is_readable($this->fileCache)) {
                throw new Exception\RuntimeException(sprintf('"%s" cannot read', $this->fileCache));
            }
            return require $this->fileCache;
        }
        return false;
    }

    /**
     * Set config to file
     * @param array $config
     */
    public function setConfig(array $config)
    {
        if (file_exists($this->fileCache)) {
            @unlink($this->fileCache);
        }
        (new PhpArray())
                ->setUseBracketArraySyntax(true)
                ->toFile($this->fileCache, $config);
    }
}
