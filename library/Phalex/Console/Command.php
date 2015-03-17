<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Console;

use Phalex\Di\Di;
use Symfony\Component\Console\Command\Command as SymCommand;

/**
 * Description of Command
 *
 * @author quangtm
 */
class Command extends SymCommand
{
    /**
     *
     * @var Di
     */
    protected $di;

    public function __construct(Di $di = null, $name = null)
    {
        parent::__construct($name);
        if ($di) {
            $this->setDI($di);
        }
    }

    public function setDI(DI $di)
    {
        $this->di = $di;
        return $this;
    }

    /**
     *
     * @return Di
     */
    public function getDI()
    {
        return $this->di;
    }
}
