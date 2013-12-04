<?php

class FilesModule extends CWebModule {
	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
		                      'files.models.*',
		                      'files.components.*',
		                 ));
	}


	public static function register () {
		self::_addBehaviors();
		self::_registerComponent();
		self::_addUrlRules();
		self::_addModelRules();
	}

	private static function _registerComponent () {
		Yii::app()->pd->registerApplicationComponents(array(
		                                                   'image' => array(
			                                                   'class' => 'application.modules.files.components.ImageHandler.CImageHandler',
		                                                   )
		                                              ));
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'files/<action:\w+>/*'                  => 'files/default/<action>',
		                                 'files/<controller:\w+>/<action:\w+>/*' => 'files/<controller>/<action>',
		                            ));
	}

	private static function _addBehaviors () {
		Yii::import('application.modules.files.models.*');

		Yii::app()->pd->registerBehavior('modules\torrents\models\TorrentGroup',
			array(
			     'image' => array(
				     'class'          => 'application.modules.files.behaviors.yii-attachment-behavior.AttachmentBehavior',
				     'types'          => array(
					     'gif',
					     'jpg',
					     'png',
					     'jpeg'
				     ),
				     'attribute'      => 'picture',
				     'maxSize'        => 1 * 1024 * 1024,
				     # Default image to return if no image path is found in the DB
				     'fallback_image' => '/images/NoImageAvailable.jpg',
				     'path'           => "uploads/images/:model/:firstTwoCharsMd5/:fileNameMd5_:id.:ext",
			     ),
			));

		Yii::app()->pd->registerBehavior('Group',
			array(
			     'image' => array(
				     'class'          => 'application.modules.files.behaviors.yii-attachment-behavior.AttachmentBehavior',
				     'types'          => array(
					     'gif',
					     'jpg',
					     'png',
					     'jpeg'
				     ),
				     'attribute'      => 'picture',
				     'maxSize'        => 1 * 1024 * 1024,
				     # Default image to return if no image path is found in the DB
				     'fallback_image' => '/images/NoImageAvailable.jpg',
				     'path'           => "uploads/images/:model/:firstTwoCharsMd5/:fileNameMd5_:id.:ext",
			     ),
			));

		Yii::app()->pd->registerBehavior('UserProfile',
			array(
			     'image' => array(
				     'class'          => 'application.modules.files.behaviors.yii-attachment-behavior.AttachmentBehavior',
				     'types'          => array(
					     'gif',
					     'jpg',
					     'png',
					     'jpeg'
				     ),
				     'maxSize'        => 1 * 1024 * 1024,
				     'attribute'      => 'picture',
				     # Default image to return if no image path is found in the DB
				     'fallback_image' => '/images/no_photo.png',
				     'path'           => "uploads/images/:model/:firstTwoCharsMd5/:fileNameMd5_:id.:ext",
			     ),
			));

		Yii::app()->pd->registerBehavior('modules\blogs\models\BlogPost',
			array(
			     '_update' => array(
				     'class' => 'application.modules.files.behaviors.UpdateModelsBehavior',
			     ),
			));
	}

	private static function _addModelRules () {
		Yii::app()->pd->addModelRules('modules\torrents\models\TorrentGroup',
			array(
			     'picture',
			     'required',
			     'on' => 'insert',
			),
			array(
			     'picture',
			     'safe',
			     'on' => 'update',
			));
		//array(
		//     'picture',
		//     'unsafe'
		//));
		/*Yii::app()->pd->addModelRules('Category',
			array(
			     'image',
			     'file',
			     'types'      => 'jpg,jpeg,gif,png',
			     //'maxSize'    => '204800',
			     'allowEmpty' => true
			),
			array('picture', 'unsafe'));*/
	}
}
