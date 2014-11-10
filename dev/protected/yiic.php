<?php
$env = getenv('APP_ENV');

switch ( $env ) {
    case 'devel':
        $config = 'devel/console.php';
        break;

    default:
        $config = 'production/console.php';
        break;
}

require_once(dirname(__FILE__) . '/config/config.php');
require_once($yiic);

