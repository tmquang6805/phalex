<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine;
use Phalex\Di\Di;

/**
 * Description of View
 *
 * @author quangtm
 */
class View extends PhalconView
{
    public function __construct(array $options)
    {
        if (!isset($options['di']) || !$options['di'] instanceof Di || !isset($options['views_dir'])) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid options for %s', __CLASS__));
        }

        parent::__construct();
        $this->setDI($options['di']);
        $this->setViewsDir($options['views_dir']);
        $engines = [
            '.phtml' => Engine\Php::class,
            '.volt'  => Engine\Volt::class,
        ];
        if (isset($options['engines'])) {
            $engines = $options['engines'];
        }
        $this->registerEngines($engines);
    }
}
