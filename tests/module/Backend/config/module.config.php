<?php

namespace Backend;

use Application;

return [
    'router' => [
        'be_home' => [
            'route'     => '/',
            'options'   => [
                'module'     => __NAMESPACE__,
                'namespace'  => namespace\Controller::class,
                'controller' => 'index',
                'action'     => 'index'
            ],
            'host_name' => 'api.test-example.com',
        ],
        'be_news' => [
            'route'        => '/news/:action/([a-z0-9-]+)-([1-9][0-9]*)\.html',
            'options'      => [
                'module'     => __NAMESPACE__,
                'namespace'  => namespace\Controller::class,
                'controller' => 'article',
                'action'     => 1,
                'title'      => 2,
                'id'         => 3,
            ],
            'convertions'  => [
                'id' => Application\Router\ConvertId::class,
            ],
            'before_match' => namespace\Router\Callback::class,
            'host_name'    => 'api.test-example.com',
        ],
    ],
    'view'   => [
        __NAMESPACE__ => __DIR__ . '/../view/'
    ],
];
