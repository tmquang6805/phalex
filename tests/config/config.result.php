<?php

use Phalcon\Db\Adapter\Pdo\Mysql;

$cwd = getcwd();

return [
    'router' => [
        'home'                 => [
            'route'       => '/',
            'definitions' => [
                'module'     => Application::class,
                'namespace'  => Application\Controller::class,
                'controller' => 'index',
                'action'     => 'index'
            ],
        ],
        'news'                 => [
            'route'       => '/news/([a-z0-9-]+)-([1-9][0-9]*)\.html',
            'definitions' => [
                'module'     => Application::class,
                'namespace'  => Application\Controller::class,
                'controller' => 'article',
                'action'     => 'detail',
                'title'      => 1,
                'id'         => 2
            ],
            'convertions' => [
                'id' => Application\Router\ConvertId::class,
            ],
        ],
        'be_home'              => [
            'route'       => '/',
            'definitions' => [
                'module'     => Backend::class,
                'namespace'  => Backend\Controller::class,
                'controller' => 'index',
                'action'     => 'index'
            ],
            'host_name'   => 'admin.test-example.com',
        ],
        'be_news'              => [
            'route'        => '/news/:action/([a-z0-9-]+)-([1-9][0-9]*)\.html',
            'definitions'  => [
                'module'     => Backend::class,
                'namespace'  => Backend\Controller::class,
                'controller' => 'article',
                'action'     => 1,
                'title'      => 2,
                'id'         => 3,
            ],
            'convertions'  => [
                'id' => Application\Router\ConvertId::class,
            ],
            'before_match' => Backend\Router\Callback::class,
            'host_name'    => 'api.test-example.com',
        ],
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
    'view'   => [
        'Application' => $cwd . '/tests/module/Application/view',
        'Backend'     => $cwd . '/tests/module/Backend/view',
    ],
    'db'     => [
        'master' => [
            'adapter'  => Mysql::class,
            'dbname'   => 'test',
            'host'     => '127.0.0.1',
            'password' => '123456',
            'username' => 'dbuser',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            ],
        ],
        'slave1' => [
            'adapter'  => Mysql::class,
            'dbname'   => 'test',
            'host'     => '127.0.0.2',
            'password' => '123456',
            'username' => 'dbuser',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            ],
        ],
        'slave2' => [
            'adapter'  => Mysql::class,
            'dbname'   => 'test',
            'host'     => '127.0.0.3',
            'password' => '123456',
            'username' => 'dbuser',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            ],
        ],
    ],
];