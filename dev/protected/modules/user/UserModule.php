<?php

class UserModule extends CWebModule {
	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'user.models.*',
		                      'user.components.*',
		                 ));
	}

	public static function register () {
		self::_registerComponent();
		self::_addUrlRules();
	}

	private static function _registerComponent () {
		Yii::app()->pd->registerApplicationComponents(array(
		                                                  'user'          => array(
			                                                  'class'          => 'application.modules.user.components.WebUser',
			                                                  'allowAutoLogin' => true,
			                                                  'loginUrl'       => '/user/login',
			                                                  'autoUpdateFlash' => false, // add this line to disable the flash counter
		                                                  )
		                                             ));
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'user/<action:\w+>/*' => 'user/default/<action>',
		                                 'user/<controller:\w+>/<action:\w+>/*' => 'user/<controller>/<action>',
		                            ));
	}
}
