<?php

class GroupsModule extends CWebModule {
	public $backendController = 'groupsBackend';
	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'groups.models.*',
		                      'groups.components.*',
		                 ));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.groups.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_setImport();
		self::_addUrlRules();
		self::_addRelations();
		self::_registerBehaviors();

		Yii::app()->pd->addAdminModule('groups', 'Models management');
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/groups/backend/<action:\w+>/*' => 'groups/groupsBackend/<action>',
		                                 'yiiadmin/groups/backend/*'              => 'groups/groupsBackend',

		                                 'groups/'                                => 'groups/default/index',
		                                 'groups/<action:\w+>/*'                  => 'groups/default/<action>',
		                                 'groups/<controller:\w+>/<action:\w+>/*' => 'groups/<controller>/<action>',
		                            ));
	}

	private static function _addRelations () {
		Yii::app()->pd->addRelations('GroupUser',
			'user',
			array(
			     CActiveRecord::BELONGS_TO,
			     'User',
			     'idUser',
			),
			'application.modules.groups.models.*');

		Yii::app()->pd->addRelations('GroupUser',
			'group',
			array(
			     CActiveRecord::BELONGS_TO,
			     'Group',
			     'idGroup',
			),
			'application.modules.groups.models.*');

		/**
		 * TODO: костыль, должно быть HAS_MANY, но иначе не работает выборка при просмотре списков членов группы
		 */
		Yii::app()->pd->addRelations('User',
			'groupUser',
			array(
			     CActiveRecord::HAS_ONE,
			     'GroupUser',
			     'idUser',
			),
			'application.modules.groups.models.*');

		Yii::app()->pd->addRelations('User',
			'groups',
			array(
			     CActiveRecord::HAS_MANY,
			     'Group',
			     'idGroup',
			     'through' => 'groupUser',
			     'condition' => 'status = :status',
			     'params' => array(
				     ':status' => GroupUser::STATUS_APPROVED,
			     )
			),
			'application.modules.groups.models.*');
	}

	private static function _registerBehaviors () {
		Yii::app()->pd->registerBehavior('Subscription',
			array(
			     'AddBlogSubscription' => array(
				     'class' => 'application.modules.groups.behaviors.AddBlogSubscription'
			     )
			));
	}

	private static function  _setImport () {
		Yii::app()->pd->setImport(array('application.modules.groups.models.*'));
	}
}
