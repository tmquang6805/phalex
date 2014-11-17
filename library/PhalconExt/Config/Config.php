<?php
/**
 * Created by PhpStorm.
 * User: quangtm
 * Date: 18/11/2014
 * Time: 00:10
 */

namespace PhalconExt\Config;


class Config
{

    private $config;

    public function __construct (array $config)
    {

    }

    public function getConfig ()
    {
        if ($this->config) {
            return $this->config;
        }

        if (($this->config = $this->getConfigFromCache()) !== false) {
            return $this->config;
        }

        return $this->getConfigModules()
            ->getConfigApp()
            ->merge()
            ->setConfigToCache()
            ->getConfig();
    }

}