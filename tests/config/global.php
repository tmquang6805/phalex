<?php

use Phalcon\Db\Adapter\Pdo\Mysql;

return [
    'db' => [
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
];
