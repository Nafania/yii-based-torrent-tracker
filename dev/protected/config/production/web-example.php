<?php
$yii = '/usr/share/php/lib/yii-1.1.15.022a51/framework/yii.php';

return array(

    // preloading 'log' component
    'preload' => array(
        'log',
        'config',
        //'debug'
        //'bootstrap',
    ),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.helpers.*',
        'application.extensions.*',
        'application.behaviors.*',

        'application.modules.yiiadmin.components.*',
    ),

    'modules' => [],

    // application components
    'components' => array(
        'pd' => array(
            'class' => 'application.components.PluginsDispatcher',
        ),

        'authManager' => array(
            'behaviors' => array(
                'auth' => array(
                    'class' => 'application.modules.auth.components.AuthBehavior',
                ),
            ),
            'class' => 'application.modules.auth.components.CachedDbAuthManager',
            'cachingDuration' => 3600,
            'defaultRoles' => array('guest'),
        ),

        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap',
            'responsiveCss' => true,
            'ajaxJsLoad' => false,
            'ajaxCssLoad' => false,
            'jqueryCss' => false,
            'minifyCss' => true,
        ),

        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'useStrictParsing' => true,
            'rules' => array(
                '/' => 'site/index',
            )
        ),

        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=yii-torrent',
            'username' => 'root',
            'password' => '',
            'schemaCachingDuration' => 3600,
            'enableParamLogging' => false,
            'enableProfiling' => false,
            'charset' => 'utf8',
            'tablePrefix' => '',
        ),

        'errorHandler' => array(
            'errorAction' => 'site/error',
            'adminInfo' => 'admin@yii-torrent'
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CEmailLogRoute',
                    'except' => 'exception.CHttpException.404, exception.CHttpException.403, exception.CHttpException.400, exception.CHttpException.401',
                    'levels' => 'error, warning, info',
                    'emails' => array('admin@yii-torrent'),
                    'sentFrom' => 'error@yii-torrent',
                    'headers' => array(
                        'Content-type: text/plain; charset="utf-8"'
                    ),
                    'filter' => array(
                        'class' => 'CLogFilter',
                        'logVars' => array(
                            '_GET',
                            '_SERVER'
                        ),
                    ),
                ),
            ),
        ),

        'cache' => array(
            'class' => 'CApcCache',
            'keyPrefix' => 'yiit_',
        ),

        'mail' => array(
            'class' => 'ext.mail.YiiMail',
            'transportType' => 'php',
            'viewPath' => 'application.views.mail',
            'logging' => false,
            'dryRun' => false
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'csrfTokenName' => 'csrf'
        ),
        'clientScript' => array(
            'class' => 'ext.ExtendedClientScript.ExtendedClientScript',
            'autoRefresh' => false,
            'compressJs' => true,
            'compressCss' => false,
            'combineJs' => true,
            'combineCss' => false,

            //'filePath' => realpath(dirname(__FILE__) . '/../../../assets'),
			//'fileUrl'     => 'http://s.' . $_SERVER['SERVER_NAME'] . '/assets',

            'packages' => [
                'common' => [
                    'baseUrl' => '/js/',
                    'js' => ['common.js'],
                    'depends' => [
                        'jquery',
                        'bbq'
                    ],
                ],
                'theme-default' => [
                    'baseUrl' => '/css/',
                    'css' => ['style.css']
                ],
                'theme-dark' => [
                    'baseUrl' => '/css/',
                    'css' => ['style.css', 'darkstrap.min.css','darkstrap-custom.css']
                ],
            ]
        ),

        /*'assetManager' => array(
            'basePath' => realpath(dirname(__FILE__) . '/../../../assets'),
			'baseUrl'  => 'http://s.' . $_SERVER['SERVER_NAME'] . '/assets',
        ),*/

        'config' => array(
            'class' => 'EConfig',
            'cache' => 3600,
        ),
        'widgetFactory' => array(
            'widgets' => array(
                'TbPager' => array('displayFirstAndLast' => true)
            ),
        ),
        'session' => [
            'class' => 'application.extensions.redis.ARedisSession',
            'keyPrefix' => 'Session:',
            'connectionID' => 'redis',
            'timeout' => 30 * 24 * 60 * 60,
            'cookieParams' => [
                'lifetime' => 30 * 24 * 60 * 60,
            ],
        ],
        'sphinx' => [
            'class' => 'system.db.CDbConnection',
            'connectionString' => 'mysql:host=127.0.0.1;port=9306',
        ],
        'redis'        => [
            'class'    => 'application.extensions.redis.ARedisConnection',
            'hostname' => 'localhost',
            'port'     => 6379,
            'database' => 2,
            'prefix'   => 'YIIT:',
        ],
        'resque' => [
            'class' => 'ext.yii-resque.RResque',
            'server' => 'localhost', // Redis server address
            'port' => '6379', // Redis server port
            'database' => 3 // Redis database number
        ],
    ),
);