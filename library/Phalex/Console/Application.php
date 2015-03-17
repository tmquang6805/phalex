<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Console;

use Phalex\Di\Di;
use Symfony\Component\Console\Application as SymApp;

/**
 * Description of Application
 *
 * @author quangtm
 */
class Application
{
    /**
     *
     * @var Di
     */
    protected $di;
    
    /**
     *
     * @var SymApp
     */
    protected $app;

    public function __construct(Di $di, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $this->di = $di;
        $this->app = new SymApp($name, $version);
    }
    
    public function run()
    {
        $config = $this->di->get('config');
        if (!isset($config['console'])) {
            throw new Exception\RuntimeException('Cannot find config for console');
        }
        $configConsole = $config['console']->toArray();
        foreach ($configConsole as $class) {
            $command = new $class;
            if (!$command instanceof Command) {
                $errMsg = sprintf('"%s" must be extends from "%s"', get_class($command), Command::class);
                throw new Exception\RuntimeException($errMsg);
            }
            $command->setDI($this->di);
            $this->app->add($command);
        }
        $this->app->run();
    }
}
