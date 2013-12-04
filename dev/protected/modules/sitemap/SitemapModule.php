<?php

class SitemapModule extends CWebModule {
	public $defaultController = 'default';

	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array( //'sitemap.models.*',
		                      'sitemap.extensions.sitemap.*',
		                 ));
	}

	public static function register () {
		self::_registerComponent();
		self::_addUrlRules();
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'sitemap.html' => 'sitemap/default/sitemap',
		                                 'sitemap.xml'  => 'sitemap/default/sitemapXml',
		                            ));
	}

	private static function _registerComponent () {
		Yii::app()->pd->registerApplicationComponents(array(
		                                                   'sitemap' => array(
			                                                   'class'                   => 'application.modules.sitemap.extensions.sitemap.SitemapComponent',
			                                                   'structureBuilderClass'   => array('class' => 'application.modules.sitemap.extensions.sitemap.components.AppStructureBuilder'),
			                                                   'structureComponentClass' => array('class' => 'application.modules.sitemap.extensions.sitemap.components.AppStructureComponent'),
		                                                   ),
		                                              ));
	}
}
