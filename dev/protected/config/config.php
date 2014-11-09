<?php

if ( $env == 'devel' ) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
}

$config = require_once($config);

$config = array_merge(
	array(
	     'basePath'       => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	     'name'           => '',

	     'sourceLanguage' => 'en',
	     'language'       => 'ru',
	     'charset'        => 'UTF-8',

	     'onBeginRequest' => array(
		     'PluginsDispatcher',
		     'load'
	     ),

	     'params'         => array(),
	),
	$config
);

/*$_host = explode('.', $_SERVER['HTTP_HOST']);
if ( $_host[0] == 'm' ) {
	$_GET['theme'] = 'mobile';
}*/

setlocale(LC_ALL, 'ru_RU.UTF-8');
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=utf-8');