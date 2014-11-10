<?php
namespace modules\userwarnings;

use Yii;
use CActiveRecord;

class UserwarningsModule extends \CWebModule {
	public $controllerNamespace = '\modules\userwarnings\controllers';

	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.userwarnings.assets'),
				false,
				-1,
				defined('YII_DEBUG'));
		}
		return $this->_assetsUrl;
	}


	public static function register () {
		self::_addUrlRules();
		self::_addRelations();
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
				'userwarnings/'                                => 'userwarnings/default/index',
				'userwarnings/<action:\w+>/*'                  => 'userwarnings/default/<action>',
				'userwarnings/<controller:\w+>/<action:\w+>/*' => 'userwarnings/<controller>/<action>',
			),
			false);
	}

	private static function _addRelations() {
		Yii::app()->pd->addRelations('User',
			'warningsCount',
			array(
				\CActiveRecord::STAT,
				'modules\userwarnings\models\UserWarning',
				'uId',
			),
			'application.modules.torrents.models.*');

		Yii::app()->pd->addRelations('User',
			'warnings',
			array(
				\CActiveRecord::HAS_MANY,
				'modules\userwarnings\models\UserWarning',
				'uId',
			),
			'application.modules.torrents.models.*');
	}
}
