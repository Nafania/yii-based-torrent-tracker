<?php
$yii = 'Z:\home\yii-1.1.14.f0fee9\framework\yii.php';

return array(

	// preloading 'log' component
	'preload'    => array(
		'log',
		'config',
		'debug',
	),

	// autoloading model and component classes
	'import'     => array(
		'application.components.*',
		'application.helpers.*',
		'application.extensions.*',

		'application.modules.yiiadmin.components.*',
	),

	'modules'    => array(
		// uncomment the following to enable the Gii tool
		'gii' => array(
			'class'          => 'system.gii.GiiModule',
			'password'       => '1234567890',
			'generatorPaths' => array(
				'bootstrap.gii'
			),
		),
	),

	// application components
	'components' => array(
		/**
		 * @var Yii::app()->pd PluginsDispatcher
		 */
		'pd'            => array(
			'class' => 'application.components.PluginsDispatcher',
		),

		'authManager'   => array(
			'behaviors'       => array(
				'auth' => array(
					'class' => 'application.modules.auth.components.AuthBehavior',
				),
			),
			'class'           => 'application.modules.auth.components.CachedDbAuthManager',
			'cachingDuration' => 3600,
			'defaultRoles'    => array('guest'),
		),

		'bootstrap'     => array(
			'class'         => 'ext.bootstrap.components.Bootstrap',
			'responsiveCss' => true,
			'ajaxJsLoad'    => false,
			'ajaxCssLoad'   => false,
			'jqueryCss'     => false,
			'minifyCss'     => true,
			//'republishAssetsOnRequest' => false,
		),

		'urlManager'    => array(
			'urlFormat'        => 'path',
			'showScriptName'   => false,
			'useStrictParsing' => true,
			'rules'            => array(
				'/'                                 => 'site/index',
				'gii'                               => 'gii',
				'gii/<controller:\w+>'              => 'gii/<controller>',
				'gii/<controller:\w+>/<action:\w+>' => 'gii/<controller>/<action>',
			)
		),

		'db'            => array(
			'connectionString'      => 'mysql:host=localhost;dbname=yii-torrent',
			'schemaCachingDuration' => 3600,
			'username'              => 'root',
			'password'              => '',
			'enableParamLogging'    => true,
			'enableProfiling'       => true,
			'charset'               => 'utf8',
			'tablePrefix'           => '',
		),

		'debug'         => array(
			'class' => 'ext.yii2-debug.Yii2Debug',
		),

		'errorHandler'  => array(
			'errorAction' => 'site/error',
			'adminInfo'   => 'admin@yii-torrent'
		),

		'log'           => array(
			'class'  => 'CLogRouter',
			'routes' => array(
				array(
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
				/*array(
					'class' => 'CWebLogRoute',
				),
				/*array(
					'class'    => 'CEmailLogRoute',
					'levels'   => 'error, warning',
					'emails'   => array('admin@stroyka'),
					'sentFrom' => 'error@stroyka',
				),*/
			),
		),

		'cache'         => array(
			'class'     => 'system.caching.CDummyCache',
			'keyPrefix' => 'tor_',
		),

		'mail'          => array(
			'class'         => 'ext.mail.YiiMail',
			'transportType' => 'php',
			'viewPath'      => 'application.views.mail',
			'logging'       => true,
			'dryRun'        => false
		),
		'request'       => array(
			'enableCsrfValidation' => true,
			'csrfTokenName'        => 'csrf'
		),
		'clientScript'  => array(
			'class'       => 'ext.ExtendedClientScript.ExtendedClientScript',
			'compressJs'  => false,
			'compressCss' => false,
			'combineJs'   => false,
			'combineCss'  => false,

			'packages'    => array(
				'common' => array(
					'baseUrl' => '/js/',
					'js'      => array('common.js'),
					'depends' => array(
						'jquery',
						'bbq'
					),
				),
			),
		),
		'config'        => array(
			'class' => 'EConfig',
			'cache' => 3600,
		),
		'widgetFactory' => array(
			'widgets' => array(
				'TbPager' => array('displayFirstAndLast' => true),
			),
		),
		'session'       => array(
			'class'                   => 'application.components.EDbHttpSession',
			'connectionID'            => 'db',
			'autoCreateSessionTable'  => false,
			'sessionName'             => 'sid',
			'useTransparentSessionID' => true,
			'autoStart'               => true,
			//'cookieMode'              => 'none',
			'sessionTableName'        => '{{sessions}}',
			'timeout'                 => 7 * 24 * 60 * 60,
			'cookieParams'            => array(
				'lifetime' => 30 * 24 * 60 * 60,
			),
		),
	)
);