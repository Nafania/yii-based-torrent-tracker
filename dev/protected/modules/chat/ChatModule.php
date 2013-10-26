<?php
class ChatModule extends CWebModule {

	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.chat.assets'), false, -1, defined('YII_DEBUG'));
		}
		return $this->_assetsUrl;
	}

	public static function register() {

	}
}
