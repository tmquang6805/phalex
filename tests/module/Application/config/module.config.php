<?php

namespace Application;

return [
    'view'   => [
        __NAMESPACE__ => __DIR__ . '/../view/'
    ],
    'error_handler' => [
        'options' => [
            'views_dir' => __DIR__ . '/../view/error',
            'template_500' => 'error.phtml',
            'template_404' => 'not-found.phtml'
        ],
    ],
    'router' => [
        'home' => [
            'route'   => '/',
            'definitions' => [
                'module'     => __NAMESPACE__,
                'namespace'  => Controller::class,
                'controller' => 'index',
                'action'     => 'index'
            ],
        ],
        'news' => [
            'route'       => '/news/([a-z0-9-]+)-([1-9][0-9]*)\.html',
            'definitions'     => [
                'module'     => __NAMESPACE__,
                'namespace'  => Controller::class,
                'controller' => 'article',
                'action'     => 'detail',
                'title'      => 1,
                'id'         => 2
            ],
            'convertions' => [
                'id' => [
                    'class_name' => Router\ConvertId::class
                ],
            ],
        ],
    ],
];
