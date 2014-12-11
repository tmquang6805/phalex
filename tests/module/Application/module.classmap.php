<?php

$dir = getcwd();

use Application\Router\ConvertId;

return [
    ConvertId::class => $dir . '/tests/module/Application/src/Router/ConvertId.php',
];
