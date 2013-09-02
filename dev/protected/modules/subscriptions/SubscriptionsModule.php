<?php
class SubscriptionsModule extends CWebModule {

	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
		$this->setImport(array(
		                      'subscriptions.models.*',
		                      'subscriptions.components.*',
		                      'subscriptions.interfaces.*',
		                 ));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.subscriptions.assets'));
		}
		return $this->_assetsUrl;
	}


	public static function register () {
		self::_addUrlRules();
		self::_addBehaviors();
		self::_setImport();
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/subscriptions/backend/<action:\w+>/*' => 'subscriptions/subscriptionsBackend/<action>',
		                                 'yiiadmin/subscriptions/backend/*'              => 'subscriptions/subscriptionsBackend',

		                                 'subscriptions/event/<action:\w+>/*'            => 'subscriptions/event/<action>',
		                                 'subscriptions/<action:\w+>/*'                  => 'subscriptions/default/<action>',
		                                 'subscriptions/<controller:\w+>/<action:\w+>/*' => 'subscriptions/<controller>/<action>',

		                            ));
	}

	private static function _addBehaviors () {
		Yii::app()->pd->registerBehavior('TorrentGroup',
			array(
			     'changesBehavior' => array(
				     'class' => 'application.modules.subscriptions.behaviors.ChangesBehavior'
			     )
			));

		Yii::app()->pd->registerBehavior('Comment',
			array(
			     'commentsAnswerBehavior' => array(
				     'class' => 'application.modules.subscriptions.behaviors.CommentsAnswerBehavior'
			     )
			));
	}

	private static function _setImport () {
		Yii::app()->pd->setImport(array(
		                               'application.modules.subscriptions.interfaces.*',
		                               'application.modules.subscriptions.models.*'
		                          ));
	}
}
