<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalcon\ExtensionTest\Mvc;

use Phalcon\Extension\Mvc\Module;
use Mockery as m;

/**
 * Description of ModuleMock
 *
 * @author quangtm
 */
class ModuleMock extends Module
{

    protected function setModuleClasses()
    {
        $appMock = m::mock('Application\\Module');
        $appMock->shouldReceive('getAutoloaderConfig')->andReturn([1, 2, 3]);

        $appMock->shouldReceive('getConfig')->andReturn([
            'Application' => [
                'view' => './application/view'
            ],
        ]);

        return [
            'Application' => $appMock,
        ];
    }

}
