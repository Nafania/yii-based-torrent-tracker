<?php
$env = getenv('APP_ENV');

switch ( $env ) {
    case 'devel':
        $config = 'devel/web.php';
        break;

    default:
        $config = 'production/web.php';
        break;
}
require_once(dirname(__FILE__) . '/protected/config/config.php');
require_once($yii);
Yii::createWebApplication($config)->run();