<?php
$yiic = dirname(__FILE__) . '/../../../../../../../yii-1.1.15.022a51/framework/yiic.php';

return array(
	// preloading 'log' component
	'preload'    => array('log'),

	// autoloading model and component classes
	'import'     => array(
		'application.models.*',
		'application.components.*',
		'application.helpers.*',
		'application.extensions.*',
		'ext.mail.YiiMailMessage',
	),

	// application components
	'components' => array(
		'urlManager'   => array(
			'baseUrl'          => 'yii-torrent',
			'urlFormat'        => 'path',
			'showScriptName'   => false,
			'useStrictParsing' => true,
		),

		'pd'           => array(
			'class' => 'application.components.PluginsDispatcher',
		),
		'db'           => array(
			'connectionString'      => 'mysql:host=localhost;dbname=yii-torrent',
			'username'              => 'root',
			'password'              => '',
			'schemaCachingDuration' => 3600,
			'enableParamLogging'    => false,
			'enableProfiling'       => false,
			'charset'               => 'utf8',
			'tablePrefix'           => '',
		),

		'errorHandler' => array(
			'errorAction' => 'site/error',
			'adminInfo'   => 'admin@yii-torrent'
		),

		'log'          => array(
			'class'  => 'CLogRouter',
			'routes' => array(
				array(
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
				array(
					'class'    => 'CEmailLogRoute',
					'levels'   => 'error, warning',
					'emails'   => array('admin@yii-torrent'),
					'sentFrom' => 'error@yii-torrent',
					'headers'  => array(
						'Content-type: text/plain; charset="utf-8"'
					),
				),
			),
		),

        'cache' => array(
            'class' => 'system.caching.CDummyCache',
        ),

		'mail'         => array(
			'class'         => 'ext.mail.YiiMail',
			'transportType' => 'php',
			'viewPath'      => 'application.views.mail',
			'logging'       => true,
			'dryRun'        => false
		),
		'config'       => array(
			'class' => 'EConfig',
			'cache' => 3600,
		),
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