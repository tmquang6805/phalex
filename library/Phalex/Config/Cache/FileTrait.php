<?php

namespace Phalex\Config\Cache;

use Zend\Config\Writer\PhpArray;

trait FileTrait
{
    /**
     *
     * @var string
     */
    protected $fileCache;
    
    protected function validateConfig(array $options)
    {
        if (!isset($options['key']) || !isset($options['dir'])) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid options when create instance %s', __CLASS__));
        }

        if (!is_string($options['key']) || empty($options['key'])) {
            throw new Exception\UnexpectedValueException('The "key" config must be string data type');
        }

        if (!is_dir($options['dir']) || !is_writable($options['dir'])) {
            throw new Exception\UnexpectedValueException('The "dir" config must be writable folder');
        }
    }

    /**
     * Get config from file cache
     * @param string $file
     * @return array|false Return array config when file existed and readable, otherwise return false
     * @throws Exception\RuntimeException
     */
    public function getConfig($file = null)
    {
        $file = !empty($file) ? $file : $this->fileCache;
        if (file_exists($file)) {
            if (!is_readable($file)) {
                throw new Exception\RuntimeException(sprintf('"%s" cannot read', $file));
            }
            return require $file;
        }
        return false;
    }

    /**
     * Set config to file
     * @param array $config
     * @param string $file
     */
    public function setConfig(array $config, $file = null)
    {
        $file = !empty($file) ? $file : $this->fileCache;
        if (file_exists($file)) {
            unlink($file);
        }
        (new PhpArray())
                ->setUseBracketArraySyntax(true)
                ->toFile($file, $config);
    }
}
