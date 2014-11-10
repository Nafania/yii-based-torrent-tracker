<?php
class DraftsModule extends CWebModule {
	private $_assetsUrl;

	public function init () {
		parent::init();
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'drafts.models.*',
		                 ));
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.drafts.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_addUrlRules();
		self::_addBehaviors();
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'drafts/'                                => 'drafts/default/index',
		                                 'drafts/<action:\w+>/*'                  => 'drafts/default/<action>',
		                                 'drafts/<controller:\w+>/<action:\w+>/*' => 'drafts/<controller>/<action>',
		                            ));
	}

	private static function _addBehaviors () {
		Yii::app()->pd->registerBehavior('modules\torrents\models\Torrent',
			array(
			     'deleteDrafts' => array(
				     'class' => 'application.modules.drafts.behaviors.DeleteDraftBehavior'
			     )
			));
		Yii::app()->pd->registerBehavior('modules\blogs\models\BlogPost',
			array(
			     'deleteDrafts' => array(
				     'class' => 'application.modules.drafts.behaviors.DeleteDraftBehavior'
			     )
			));
	}
}
