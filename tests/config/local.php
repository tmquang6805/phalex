<?php

return [
    'db' => [
        'master' => [
            'host' => '127.0.0.1',
            'password' => '123456',
            'username' => 'dbuser',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ],
        ],
        'slave1' => [
            'host' => '127.0.0.2',
            'password' => '123456',
            'username' => 'dbuser',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ],
        ],
        'slave2' => [
            'host' => '127.0.0.3',
            'password' => '123456',
            'username' => 'dbuser',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ],
        ],
    ],
];
