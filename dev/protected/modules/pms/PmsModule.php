<?php

class PmsModule extends CWebModule {
	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'pms.models.*',
		                      'pms.components.*',
		                 ));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.pms.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_setImport();
		self::_addUrlRules();
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'pms/'                                => 'pms/default/index',
		                                 'pms/<action:\w+>/*'                  => 'pms/default/<action>',
		                                 'pms/<controller:\w+>/<action:\w+>/*' => 'pms/<controller>/<action>',
		                            ));
	}

	private static function _setImport () {
		Yii::app()->pd->setImport(array('application.modules.pms.models.*'));
	}
}
