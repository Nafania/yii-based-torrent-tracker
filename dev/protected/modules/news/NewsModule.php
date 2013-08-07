<?php

class NewsModule extends CWebModule
{
	public $backendController = 'newsBackend';
	public $defaultController = 'default';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'news.models.*',
			'news.components.*',
		));
	}

	public static function register () {
		self::_addUrlRules();

		Yii::app()->pd->addAdminModule('news', 'news management');
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/news/backend/<action:\w+>/*' => 'news/newsBackend/<action>',
		                                 'yiiadmin/news/backend/*'              => 'news/newsBackend',

		                                 'news/<action:\w+>/*' => 'news/default/<action>',
		                                 'news/<controller:\w+>/<action:\w+>/*' => 'news/<controller>/<action>',
		                            ));
	}
}
