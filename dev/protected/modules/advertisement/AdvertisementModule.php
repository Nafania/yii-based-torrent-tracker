<?php
class AdvertisementModule extends CWebModule {
	public $backendController = 'advertisementBackend';

	public function init () {
		parent::init();
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'advertisement.models.*',
		                 ));
	}

	public static function register () {
		self::_addUrlRules();
		self::_setImport();

		Yii::app()->pd->addAdminModule('advertisement', 'modules');
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/advertisement/backend/<action:\w+>/*' => 'advertisement/advertisementBackend/<action>',
		                                 'yiiadmin/advertisement/backend/*'              => 'advertisement/advertisementBackend',
		                            ), false);
	}

	private static function _setImport () {
		Yii::app()->pd->setImport(array('application.modules.advertisement.models.*'));
	}
}
