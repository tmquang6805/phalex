<?php

namespace Backend;

return [
    'namespaces' => [
        namespace\Controller::class => __DIR__ . '/../src/Controller',
    ],
    'classmap'   => [
        __DIR__ . '/../module.classmap.php'
    ]
];