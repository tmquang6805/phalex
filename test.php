<?php

$output = array();
$return = null;
$fileName = 'library/Phalex/Di/DiManager.php';
exec("php-cs-fixer fix --dry-run --level=psr2 --verbose " . escapeshellarg($fileName), $output, $return);
xdebug_var_dump($output, $return);
