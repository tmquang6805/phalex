<?php

$dir = getcwd();

return array(
    'namespaces' => array(
        'Application\\Controller' => $dir . '/tests/module/Application/src/Controller',
        'Backend\\Controller'     => $dir . '/tests/module/Backend/src/Controller',
    ),
    'classmap'   => array(
        0 => $dir . '/tests/module/Application/module.classmap.php',
        1 => $dir . '/tests/module/Backend/module.classmap.php',
    ),
);
