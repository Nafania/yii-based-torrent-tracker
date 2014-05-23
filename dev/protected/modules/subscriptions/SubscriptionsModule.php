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

	/**
	 * @return array
	 */
	public function getEventsMenu () {
		$events = Event::model()->unreaded()->forCurrentUser()->findAll();

		$eventItems = array();

		foreach ( $events AS $event ) {
			$icon = $event->getIcon();
			$eventItems[] = array(
				'label'       => '<i class="icon-' . $icon . '"></i> ' . CHtml::encode($event->getTitle()),
				'url'         => $event->getUrl(),
				'linkOptions' => array(
					'data-toggle'    => 'tooltip',
					'title'          => $event->getText(),
					'data-placement' => 'right',
				)
			);
		}

		return $eventItems;
	}


	public static function register () {
		self::_addUrlRules();
		self::_addBehaviors();
		self::_setImport();
		self::_addRelations();
		self::_addCommandsPath();
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

	private static function _addRelations () {
		Yii::app()->pd->addRelations('modules\torrents\models\TorrentGroup',
			'subscriptions',
			array(
				CActiveRecord::HAS_MANY,
				'Subscription',
				'modelId',
				'condition' => 'modelName = :modelName',
				'params'    => array(
					'modelName' => 'TorrentGroup'
				)
			),
			'application.modules.subscriptions.models.*');
	}

	private static function _addBehaviors () {
		Yii::app()->pd->registerBehavior('modules\torrents\models\TorrentGroup',
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

		Yii::app()->pd->registerBehavior('Comment',
			array(
				'blogCommentBehavior' => array(
					'class' => 'application.modules.subscriptions.behaviors.BlogCommentBehavior'
				)
			));

		Yii::app()->pd->registerBehavior('GroupUser',
			array(
				'groupUserSubscription' => array(
					'class' => 'application.modules.subscriptions.behaviors.GroupUserSubscription'
				)
			));

		Yii::app()->pd->registerBehavior('GroupUser',
			array(
				'groupUserInvited' => array(
					'class' => 'application.modules.subscriptions.behaviors.GroupUserInvited'
				)
			));

		Yii::app()->pd->registerBehavior('GroupUser',
			array(
				'groupUserNew' => array(
					'class' => 'application.modules.subscriptions.behaviors.GroupUserNew'
				)
			));

		Yii::app()->pd->registerBehavior('modules\blogs\models\BlogPost',
			array(
				'blogPostSubscription' => array(
					'class' => 'application.modules.subscriptions.behaviors.BlogPostSubscription'
				)
			));

        Yii::app()->pd->registerBehavior('modules\blogs\models\BlogPost',
      			array(
      				'blogPostUserSubscription' => array(
      					'class' => 'application.modules.subscriptions.behaviors.BlogPostUserSubscription'
      				)
      			));

		Yii::app()->pd->registerBehavior('PrivateMessage',
			array(
				'pmBehavior' => array(
					'class' => 'application.modules.subscriptions.behaviors.PmBehavior'
				)
			));

		Yii::app()->pd->registerBehavior('Comment',
			array(
				'torrentCommentBehavior' => array(
					'class' => 'application.modules.subscriptions.behaviors.TorrentCommentBehavior'
				)
			));

		Yii::app()->pd->registerBehavior('modules\userwarnings\models\UserWarning',
			array(
				'userWarningBehavior' => array(
					'class' => 'application.modules.subscriptions.behaviors.UserWarningBehavior'
				)
			));
	}

	private static function _setImport () {
		Yii::app()->pd->setImport(array(
			'application.modules.subscriptions.interfaces.*',
			'application.modules.subscriptions.models.*'
		));
	}


	private static function _addCommandsPath () {
		Yii::app()->pd->addCommandsPath('application.modules.subscriptions.commands');
	}
}
