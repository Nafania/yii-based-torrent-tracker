<?php

class BlogsModule extends CWebModule {
	public $backendController = 'blogsBackend';
	public $defaultController = 'default';

	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'blogs.models.*',
		                      'blogs.components.*',
		                 ));
	}


	public static function register () {
		self::_addUrlRules();

		Yii::app()->pd->addAdminModule('blogs', 'Models management');
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/blogs/backend/<action:\w+>/*' => 'blogs/blogsBackend/<action>',
		                                 'yiiadmin/blogs/backend/*'              => 'blogs/blogsBackend',

		                                 'blogs/post/<action:\w+>/*'             => 'blogs/post/<action>',
		                                 'blogs/<action:\w+>/*'                  => 'blogs/default/<action>',
		                                 'blogs/<controller:\w+>/<action:\w+>/*' => 'blogs/<controller>/<action>',
		                            ));
	}
}
