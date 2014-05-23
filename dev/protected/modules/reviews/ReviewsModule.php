<?php

class ReviewsModule extends CWebModule {
	public $backendController = 'reviewsBackend';
	private $_assetsUrl;

	public function init () {
		parent::init();

		$this->setImport(array(
			'reviews.components.*',
		));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.reviews.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_addUrlRules();
        self::_addCommandsPath();
		Yii::app()->pd->addAdminModule('reviews', 'modules');
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
			'yiiadmin/reviews/backend/<action:\w+>/*' => 'reviews/reviewsBackend/<action>',
			'yiiadmin/reviews/backend/*'              => 'reviews/reviewsBackend',
		));
	}

    private static function _addCommandsPath () {
   		Yii::app()->pd->addCommandsPath('application.modules.reviews.commands');
   	}
}
