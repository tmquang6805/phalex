<?php

namespace Application;

return [
    'view'   => [
        __NAMESPACE__ => __DIR__ . '/../view/'
    ],
    'router' => [
        'home' => [
            'route'   => '/',
            'options' => [
                'module'     => __NAMESPACE__,
                'namespace'  => namespace\Controller::class,
                'controller' => 'index',
                'action'     => 'index'
            ],
        ],
        'news' => [
            'route'       => '/news/([a-z0-9-]+)-([1-9][0-9]*)\.html',
            'options'     => [
                'module'     => __NAMESPACE__,
                'namespace'  => namespace\Controller::class,
                'controller' => 'article',
                'action'     => 'detail',
                'title'      => 1,
                'id'         => 2
            ],
            'convertions' => [
                'id' => namespace\Router\ConvertId::class,
            ],
        ],
    ],
];
