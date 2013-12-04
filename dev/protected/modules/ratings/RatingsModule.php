<?php

class RatingsModule extends CWebModule {
	public $backendController = 'ratingsBackend';
	public $defaultController = 'default';

	public $useCronReCalc = false;

	private $_assetsUrl;

	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'ratings.models.*',
		                      'ratings.components.*',
		                      'ratings.behaviors.*'
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
		self::_addBehaviors();
		self::_setImport();
		self::_addCommandsPath();

		Yii::app()->pd->addAdminModule('ratings');
	}

	public function getRatingCoefficient ( $num ) {
		$ratings = Yii::app()->config->get('ratingsModule.ratings');
		if ( !$ratings = @unserialize($ratings) ) {
			$ratings = array();
			for ( $i = 0; $i < 17; ++$i ) {
				$ratings[] = 0;
			}
		}

		return (isset($ratings[$num]) ? $ratings[$num] : 0);
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/ratings/backend/<action:\w+>/*' => 'ratings/ratingsBackend/<action>',
		                                 'yiiadmin/ratings/backend/*'              => 'ratings/ratingsBackend',

		                                 'ratings/<action:\w+>/*'                  => 'ratings/default/<action>',
		                                 'ratings/<controller:\w+>/<action:\w+>/*' => 'ratings/<controller>/<action>',
		                            ));
	}

	private static function _addRelations () {

		Yii::app()->pd->addRelations('User',
			'rating',
			array(
			     CActiveRecord::HAS_ONE,
			     'Rating',
			     'modelId',
			     'joinType' => 'LEFT JOIN',
			     'on'       => 'rating.modelName = \'User\'',
			     'together' => true,
			),
			'application.modules.ratings.models.*');

		Yii::app()->pd->addRelations('modules\blogs\models\BlogPost',
			'rating',
			array(
			     CActiveRecord::HAS_ONE,
			     'Rating',
			     'modelId',
			     'joinType' => 'LEFT JOIN',
			     'on'       => 'rating.modelName = \'BlogPost\'',
			     'together' => true,
			),
			'application.modules.ratings.models.*');

		Yii::app()->pd->addRelations('modules\torrents\models\TorrentGroup',
			'ratings',
			array(
			     CActiveRecord::HAS_MANY,
			     'RatingRelations',
			     'modelId',
			     'condition' => 'modelName = :modelName',
			     'params'    => array(
				     'modelName' => 'TorrentGroup'
			     )
			),
			'application.modules.ratings.models.*');
	}

	private static function _addBehaviors () {
		Yii::app()->pd->registerBehavior('User',
			array(
			     'userRatingBehavior' => array(
				     'class' => 'application.modules.ratings.behaviors.UserRatingBehavior'
			     )
			));
		Yii::app()->pd->registerBehavior('Blog',
			array(
			     'blogRatingBehavior' => array(
				     'class' => 'application.modules.ratings.behaviors.BlogRatingBehavior'
			     )
			));
		Yii::app()->pd->registerBehavior('modules\blogs\models\BlogPost',
			array(
			     'blogPostRatingBehavior' => array(
				     'class' => 'application.modules.ratings.behaviors.BlogPostRatingBehavior'
			     )
			));

		Yii::app()->pd->registerBehavior('Comment',
			array(
			     'commentRatingBehavior' => array(
				     'class' => 'application.modules.ratings.behaviors.CommentRatingBehavior'
			     )
			));
		Yii::app()->pd->registerBehavior('modules\torrents\models\TorrentGroup',
			array(
			     'torrentRatingBehavior' => array(
				     'class' => 'application.modules.ratings.behaviors.TorrentRatingBehavior'
			     )
			));

		Yii::app()->pd->registerBehavior('Group',
			array(
			     'groupRatingBehavior' => array(
				     'class' => 'application.modules.ratings.behaviors.GroupRatingBehavior'
			     )
			));
	}

	private static function _setImport () {
		Yii::app()->pd->setImport(array('application.modules.ratings.behaviors.*'));
		Yii::app()->pd->setImport(array('application.modules.ratings.models.*'));
	}

	private static function _addCommandsPath () {
		Yii::app()->pd->addCommandsPath('application.modules.ratings.commands');
	}
}
