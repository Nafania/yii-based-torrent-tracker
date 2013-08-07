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

		Yii::app()->pd->registerBehavior('TorrentGroup',
			array(
			     'image' => array(
				     'class'     => 'application.modules.files.behaviors.yii-attachment-behavior.AttachmentBehavior',
				     'types'     => array(
					     'gif',
					     'jpg',
					     'png',
					     'jpeg'
				     ),
				     'attribute' => 'picture',
				     # Default image to return if no image path is found in the DB
				     //'fallback_image' => 'images/sample_image.gif',
				     'path'      => "uploads/images/:model/:id/:fileNameMd5.:ext",
			     ),
			));

		Yii::app()->pd->registerBehavior('UserProfile',
			array(
			     'image' => array(
				     'class'     => 'application.modules.files.behaviors.yii-attachment-behavior.AttachmentBehavior',
				     'types'     => array(
					     'gif',
					     'jpg',
					     'png',
					     'jpeg'
				     ),
				     'attribute' => 'picture',
				     # Default image to return if no image path is found in the DB
				     'fallback_image' => '/images/profile_mask2.png',
				     'path'      => "uploads/images/:model/:id/:fileNameMd5.:ext",
			     ),
			));

		/*Yii::app()->pd->registerBehavior('Category',
			array(
			     'image' => array(
				     'class'     => 'application.modules.files.behaviors.yii-attachment-behavior.AttachmentBehavior',
				     'types'     => array(
					     'gif',
					     'jpg',
					     'png',
					     'jpeg'
				     ),
				     'attribute' => 'picture',
				     # Default image to return if no image path is found in the DB
				     //'fallback_image' => 'images/sample_image.gif',
				     'path'      => "uploads/images/:model/:id/:fileNameMd5.:ext",
			     ),
			));*/
	}

	private function _addModelRules () {
		Yii::app()->pd->addModelRules('TorrentGroup',
			array(
			     'picture',
			     'required',
			     'on' => 'insert'
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
