<?php
class SavedsearchesModule extends CWebModule {

	public function init () {
		parent::init();
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'savedsearches.models.*',
		                      'savedsearches.behaviors.*',
		                 ));
	}

	public static function register () {
		self::_addUrlRules();
		self::_registerBehaviors();
		self::_setImport();
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array('savedsearches/<action:\w+>/*' => 'savedsearches/default/<action>',));
	}

	private static function _setImport () {
		Yii::app()->pd->setImport(array('application.modules.savedsearches.models.*'));
	}

	private static function _registerBehaviors () {
		Yii::app()->pd->registerBehavior('WebUser',
			array(
			     'savedSearch' => array(
				     'class' => 'application.modules.savedsearches.behaviors.UserSavedSearch'
			     )
			));
		Yii::app()->pd->registerBehavior('modules\torrents\models\TorrentGroup',
			array(
			     'torrentGroupSearch' => array(
				     'class' => 'application.modules.savedsearches.behaviors.TorrentGroupSearch'
			     )
			));
		Yii::app()->pd->registerBehavior('modules\torrents\models\Torrent',
			array(
			     'torrentSearch' => array(
				     'class' => 'application.modules.savedsearches.behaviors.TorrentSearch'
			     )
			));
	}
}
