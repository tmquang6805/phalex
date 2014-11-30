<?php

use Phalcon\Db\Adapter\Pdo\Mysql;

return [
    'db'     => [
        'master' => [
            'adapter' => Mysql::class,
            'dbname'  => 'test',
            'options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ],
        'slave1' => [
            'adapter' => Mysql::class,
            'dbname'  => 'test',
            'options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ],
        'slave2' => [
            'adapter' => Mysql::class,
            'dbname'  => 'test',
            'options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ],
    ],
    'router' => [
        'api_add_article'      => [
            'route'       => '/article',
            'definitions' => [
                'module'     => Application::class,
                'namespace'  => Application\Controller::class,
                'controller' => 'api-article',
                'action'     => 'add'
            ],
            'methods'     => ['post'],
            'host_name'   => 'api.test-example.com',
        ],
        'api_get_edit_article' => [
            'route'       => '/article/([1-9][0-9]*)',
            'definitions' => [
                'module'     => Application::class,
                'namespace'  => Application\Controller::class,
                'controller' => 'api-article',
                'action'     => 'get-edit',
                'id'         => 1,
            ],
            'methods'     => ['get', 'put'],
            'host_name'   => 'api.test-example.com',
        ],
    ],
];
