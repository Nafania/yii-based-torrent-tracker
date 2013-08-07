<?php

class RatingsModule extends CWebModule {
	public $backendController = 'ratingsBackend';
	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'ratings.models.*',
		                      'ratings.components.*',
		                 ));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.ratings.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_addUrlRules();
		self::_addRelations();

		//Yii::app()->pd->addAdminModule('ratings', 'ratings management');
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/ratings/backend/<action:\w+>/*' => 'ratings/ratingsBackend/<action>',
		                                 'yiiadmin/ratings/backend/*'              => 'ratings/ratingsBackend',

		                                 'ratings/<action:\w+>/*'                  => 'ratings/default/<action>',
		                                 'ratings/<controller:\w+>/<action:\w+>/*' => 'ratings/<controller>/<action>',
		                            ));
	}

	private function _addRelations () {
		Yii::app()->pd->addRelations('Comment',
			'rating',
			array(
			     CActiveRecord::HAS_ONE,
			     'Rating',
			     'modelId',
			     'condition' => 'rating.modelName = \'Comment\'',
			     'together'  => true,
			),
			'application.modules.ratings.models.*');

		Yii::app()->pd->addRelations('User',
			'rating',
			array(
			     CActiveRecord::HAS_ONE,
			     'Rating',
			     'modelId',
			     'condition' => 'rating.modelName = \'User\'',
			     'together'  => true,
			),
			'application.modules.ratings.models.*');
	}

}
