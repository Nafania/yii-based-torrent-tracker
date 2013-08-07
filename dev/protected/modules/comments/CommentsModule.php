<?php

class CommentsModule extends CWebModule
{
	private $_assetsUrl;

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'comments.models.*',
			'comments.components.*',
		    'comments.helpers.*'
		));
	}

	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.comments.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_addUrlRules();
		self::_addModelsRelations();
		self::_addBehaviors();
	}

	private  static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/comments/backend/<action:\w+>/*' => 'comments/commentsBackend/<action>',
		                                 'yiiadmin/comments/backend/*'              => 'comments/commentsBackend',

		                                 'comments/<action:\w+>/*'                  => 'comments/default/<action>',
		                                 'comments/<controller:\w+>/<action:\w+>/*' => 'comments/<controller>/<action>',
		                            ));
	}

	private static function _addModelsRelations () {
		Yii::app()->pd->addRelations('Comment',
			'user',
			array(
			     CActiveRecord::BELONGS_TO,
			     'User',
			     'ownerId',
			),
			'application.modules.comments.models.*');
	}

	private static function _addBehaviors () {
	}
}
