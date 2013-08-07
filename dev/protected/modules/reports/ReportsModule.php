<?php

class ReportsModule extends CWebModule
{
	public $backendController = 'reportsBackend';
	public $defaultController = 'default';

	private $_assetsUrl;

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'reports.models.*',
			'reports.components.*',
		));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.reports.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_addUrlRules();

		Yii::app()->pd->addAdminModule('reports', 'reports management');
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/reports/backend/<action:\w+>/*' => 'reports/reportsBackend/<action>',
		                                 'yiiadmin/reports/backend/*'              => 'reports/reportsBackend',

		                                 'reports/<action:\w+>/*' => 'reports/default/<action>',
		                                 'reports/<controller:\w+>/<action:\w+>/*' => 'reports/<controller>/<action>',
		                            ));
	}

	private function _addRelations() {

	}
}
