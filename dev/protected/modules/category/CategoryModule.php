<?php
class CategoryModule extends CWebModule {
	public $backendController = 'categoryBackend';
	public $defaultController = 'default';

	public function init () {
		parent::init();
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'category.models.*',
		                      'category.components.*',
		                      'category.extensions.nestedset.*',
		                 ));
	}

	public static function register () {
		Yii::app()->pd->addAdminModule('category', 'category management');
		self::_addUrlRules();
		self::_addBehaviors();
		self::_addRelations();
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/category/backend/<action:\w+>/*' => 'category/categoryBackend/<action>',
		                                 'yiiadmin/category/backend/*'              => 'category/categoryBackend',

		                                 'category/<action:\w+>/*'                  => 'category/default/<action>',
		                                 'category/<controller:\w+>/<action:\w+>/*' => 'category/<controller>/<action>',
		                            ));
	}

	private function _addBehaviors () {
		Yii::app()->pd->registerBehavior('Category',
			array(
			     'nestedSetBehavior' => array(
				     'class'          => 'application.modules.category.extensions.nestedsetbehavior.NestedSetBehavior',
				     'leftAttribute'  => 'lft',
				     'rightAttribute' => 'rgt',
				     'levelAttribute' => 'level',
				     'hasManyRoots'   => true,
			     ),
			));
	}

	private function _addRelations() {

		Yii::app()->pd->addRelations('TorrentGroup',
			'category',
			array(
			     CActiveRecord::BELONGS_TO,
			     'Category',
			     'cId'
			),
			'application.modules.category.models.*');

		Yii::app()->pd->addRelations('Attribute',
			'category',
			array(
			     CActiveRecord::BELONGS_TO,
			     'Category',
			     'cId'
			),
			'application.modules.category.models.*');
	}
}
