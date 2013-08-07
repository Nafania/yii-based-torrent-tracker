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
		self::_addBehaviors();
		self::_addModelsRelations();

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


	private static function _addModelsRelations () {
		Yii::app()->pd->addRelations('Comment',
			'torrentComments',
			array(
			     CActiveRecord::HAS_ONE,
			     'TorrentCommentsRelations',
			     'commentId',
			),
			'application.modules.torrents.models.*');

		Yii::app()->pd->addRelations('Comment',
			'torrent',
			array(
			     CActiveRecord::HAS_ONE,
			     'Torrent',
			     'torrentId',
			     'through' => 'torrentComments'
			),
			'application.modules.torrents.models.*');
	}

	private function _addModelRules () {
		Yii::app()->pd->addModelRules('Category',
			array(
			     'id',
			     'required',
			     'on' => 'createTorrent',
			));

		Yii::app()->pd->addModelRules('Category',
			array(
			     'torrentsNameRules',
			     'safe',
			));

		Yii::app()->pd->addModelRules('Comment',
			array(
			     'torrentId',
			     'safe',
			));
	}

	private function _addBehaviors () {
		Yii::app()->pd->registerBehavior('Category',
			array(
			     'torrentNameRulesBehavior' => array(
				     'class' => 'application.modules.torrents.behaviors.TorrentNameRuleBehavior'
			     )
			));

		Yii::app()->pd->registerBehavior('Comment',
			array(
			     'torrentComments' => array(
				     'class' => 'application.modules.torrents.behaviors.TorrentCommentsRelationsBehavior'
			     )
			));
	}
}
