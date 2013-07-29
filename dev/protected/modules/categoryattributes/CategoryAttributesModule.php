<?php
class CategoryAttributesModule extends CWebModule {
	public $backendController = 'categoryAttributesBackend';

	private $_assetsUrl;

	public function init () {
		parent::init();
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'categoryattributes.models.*',
		                      'categoryattributes.components.*',
		                 ));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.categoryattributes.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		Yii::app()->pd->addAdminModule('categoryattributes', 'category management');
		self::_addUrlRules();
		self::_addBehaviors();
		self::_addModelRules();
		self::_addRelations();
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/categoryattributes/backend/<action:\w+>/*' => 'categoryattributes/categoryAttributesBackend/<action>',
		                                 'yiiadmin/categoryattributes/backend/*'              => 'categoryattributes/categoryAttributesBackend',
		                            ));
	}

	private function _addBehaviors () {
		Yii::import('application.modules.categoryattributes.models.*');

		Yii::app()->pd->registerBehavior('Category',
			array(
			     'attributesBehavior' => array(
				     'class' => 'application.modules.categoryattributes.behaviors.CategoryBehavior'
			     )
			));
	}

	private function _addModelRules () {
		Yii::app()->pd->addModelRules('Category',
			array(
			     'categoryAttributes',
			     'safe',
			));
	}

	private function _addRelations () {
		Yii::app()->pd->addRelations('Category',
			'categoryattributes',
			array(
			     CActiveRecord::HAS_MANY,
			     'CategoryAttribute',
			     'catId'
			),
			'application.modules.categoryattributes.models.*');

		Yii::app()->pd->addRelations('Category',
			'attrs',
			array(
			     CActiveRecord::HAS_MANY,
			     'Attribute',
			     array('attrId' => 'id'),
			     'through' => 'categoryattributes'
			),
			'application.modules.categoryattributes.models.*');
	}
}
