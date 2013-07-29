<?php
class TorrentsModule extends CWebModule {

	public $backendController = 'torrentsBackend';
	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
		$this->setImport(array(
		                      'torrents.models.*',
		                      'torrents.components.*',
		                 ));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.torrents.assets'));
		}
		return $this->_assetsUrl;
	}


	public static function register () {
		self::_addUrlRules();
		self::_addModelRules();

		Yii::app()->pd->addAdminModule('torrents', 'Models management');
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/torrents/backend/<action:\w+>/*' => 'torrents/torrentsBackend/<action>',
		                                 'yiiadmin/torrents/backend/*'              => 'torrents/torrentsBackend',

		                                 'torrents/'                                => 'torrents/default/index',
		                                 'torrents/<action:\w+>/*'                  => 'torrents/default/<action>',
		                                 'torrents/<controller:\w+>/<action:\w+>/*' => 'torrents/<controller>/<action>',
		                            ));
	}

	private function _addModelRules () {
		Yii::app()->pd->addModelRules('Category', array(
		                                   'id',
		                                   'required',
		                                   'on' => 'createTorrent',
		                              ));
	}
}
