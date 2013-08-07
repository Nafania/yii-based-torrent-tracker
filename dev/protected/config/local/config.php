<?php
$yii = 'Z:\home\yii-1.1.13.e9e4a0\framework\yii.php';

return array(

	// preloading 'log' component
	'preload'    => array(
		'log',
		'config',
		//'bootstrap',
	),

	// autoloading model and component classes
	'import'     => array(
		'application.models.*',
		'application.components.*',
		'application.helpers.*',
		'application.extensions.*',
		'application.behaviors.*',

		'application.modules.yiiadmin.components.*',
	),

	'modules'    => array(
		// uncomment the following to enable the Gii tool
		'gii' => array(
			'class'    => 'system.gii.GiiModule',
			'password' => '1234567890',
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
		'pd'           => array(
			'class' => 'application.components.PluginsDispatcher',
		),

		'authManager'  => array(
			'behaviors'       => array(
				'auth' => array(
					'class'  => 'application.modules.auth.components.AuthBehavior',
					'admins' => array(
						'admin',
					),
					// users with full access
				),
			),
			'class'           => 'application.modules.auth.components.CachedDbAuthManager',
			'cachingDuration' => 3600,
			'defaultRoles'    => array('guest'),
		),

		'bootstrap'    => array(
			'class'                    => 'ext.bootstrap.components.Bootstrap',
			'responsiveCss'            => true,
			//'republishAssetsOnRequest' => false,
		),

		'urlManager'   => array(
			'urlFormat'        => 'path',
			'showScriptName'   => false,
			'useStrictParsing' => true,
			'rules'            => array(
				'/' => 'site/index',
				'gii'                               => 'gii',
				'gii/<controller:\w+>'              => 'gii/<controller>',
				'gii/<controller:\w+>/<action:\w+>' => 'gii/<controller>/<action>',
			)
		),

		'db'           => array(
			'connectionString'      => 'mysql:host=localhost;dbname=yii-torrent',
			'schemaCachingDuration' => 3600,
			'username'              => 'root',
			'password'              => '',
			'enableParamLogging'    => true,
			'enableProfiling'       => true,
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
				// uncomment the following to show log messages on web pages
				array( // configuration for the toolbar
					'class'     => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
					'ipFilters' => array(
						'127.0.0.1',
						'::1',
						'192.168.1.2',
						'192\.168\.1\.[0-9]{3}'
					),
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

		'cache'        => array(
			'class' => 'system.caching.CFileCache',
		),

		'mail'         => array(
			'class'         => 'ext.mail.YiiMail',
			'transportType' => 'php',
			'viewPath'      => 'application.views.mail',
			'logging'       => true,
			'dryRun'        => false
		),
		'request'      => array(
			'enableCsrfValidation' => true,
			'csrfTokenName'        => 'csrf'
		),
		'clientScript' => array(
			'class'    => 'ext.nsclientscript.NLSClientScript',
			//'excludePattern' => '/\.tpl/i', //js regexp, files with matching paths won't be filtered is set to other than 'null'
			//'includePattern' => '/\.php/', //js regexp, only files with matching paths will be filtered if set to other than 'null'

			'mergeJs'  => false,
			//def:true
			'compressMergedJs'      => false,
			//def:false

			'mergeCss' => false,
			//def:true
			'compressMergedCss'     => false,
			//def:false

			//'serverBaseUrl'         => 'http://localhost',
			//can be optionally set here
			//'mergeAbove'            => 1,
			//def:1, only "more than this value" files will be merged,
			//'curlTimeOut'           => 5,
			//def:5, see curl_setopt() doc
			//'curlConnectionTimeOut' => 10,
			//def:10, see curl_setopt() doc

			//'appVersion'            => 1.0
			//if set, it will be appended to the urls of the merged scripts/css*/
			'packages' => array(
				'common' => array(
					'baseUrl' => '/js/',
					'js'      => array('common.js'),
					'depends' => array(
						'jquery',
						'bbq'
					),
				),
			)
		),

		'config'       => array(
			'class' => 'EConfig',
			'cache' => 3600,
		),
	),
);