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
		self::_setImport();
	}

	private static function _registerComponent () {
		Yii::app()->pd->registerApplicationComponents(array(
		                                                   'user'  => array(
			                                                   'class'                     => 'application.modules.user.components.WebUser',
			                                                   'allowAutoLogin'            => true,
			                                                   'loginUrl'                  => '/user/login',
			                                                   //'loginRequiredAjaxResponse' => 'logged-out',
			                                                   'autoUpdateFlash'           => false,
			                                                   // add this line to disable the flash counter
		                                                   ),
		                                                   'loid'  => array(
			                                                   'class' => 'application.modules.user.extensions.lightopenid.loid',
		                                                   ),
		                                                   'eauth' => array(
			                                                   'class'    => 'application.modules.user.extensions.eauth.EAuth',
			                                                   'popup'    => true,
			                                                   // Использовать всплывающее окно вместо перенаправления на сайт провайдера
			                                                   'services' => array( // Вы можете настроить список провайдеров и переопределить их классы
				                                                   'google'   => array(
					                                                   'class' => 'CustomGoogleService',
				                                                   ),
				                                                   'yandex'   => array(
					                                                   'class' => 'CustomYandexService',
				                                                   ),
				                                                   'facebook' => array(
					                                                   'class'         => 'CustomFacebookService',
					                                                   'client_id'     => '',
					                                                   'client_secret' => '',
				                                                   )
			                                                   ),

		                                                   ),
		                                              ));
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'user/<action:\w+>/*'                  => 'user/default/<action>',
		                                 'user/<controller:\w+>/<action:\w+>/*' => 'user/<controller>/<action>',
		                            ));
	}

	private static function _setImport () {

		Yii::app()->pd->setImport(array(
		                               'application.modules.user.extensions.eoauth.*',
		                               'application.modules.user.extensions.eoauth.lib.*',
		                               'application.modules.user.extensions.lightopenid.*',
		                               'application.modules.user.extensions.eauth.*',
		                               'application.modules.user.extensions.eauth.services.*',
		                               'application.modules.user.extensions.eauth.custom_services.*',
		                          ));
	}
}
