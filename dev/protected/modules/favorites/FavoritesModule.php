<?php
namespace modules\favorites;

use Yii;

class FavoritesModule extends \CWebModule {
	public $controllerNamespace = '\modules\favorites\controllers';

	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.favorites.assets'),
				false,
				-1,
				defined('YII_DEBUG'));
		}
		return $this->_assetsUrl;
	}


	public static function register () {
		self::_addUrlRules();
		self::_addBehaviors();
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
				'favorites/'                                => 'favorites/default/index',
				'favorites/<action:\w+>/*'                  => 'favorites/default/<action>',
				'favorites/<controller:\w+>/<action:\w+>/*' => 'favorites/<controller>/<action>',
			),
			false);
	}

	private static function _addBehaviors () {
		Yii::app()->pd->registerBehavior('modules\torrents\models\TorrentGroup',
			array(
				'favorites' => array(
					'class' => 'application.modules.favorites.behaviors.FavoritesBehavior'
				)
			));
	}
}
