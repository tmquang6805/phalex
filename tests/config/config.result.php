<?php

use Phalcon\Db\Adapter\Pdo\Mysql;

$cwd = getcwd();

return [
    'view' => [
        'Application' => $cwd . '/tests/module/Application/view',
        'Backend'     => $cwd . '/tests/module/Backend/view',
    ],
    'db'   => [
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
