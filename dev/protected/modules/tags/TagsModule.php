<?php

class TagsModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'tags.models.*',
			'tags.components.*',
		));
	}

	public static function register () {
		self::_addUrlRules();
		self::_addBehaviors();
	}

	protected static function _addBehaviors () {
		Yii::app()->pd->registerBehavior('Torrent', array(
		        'tags' => array(
		            'class' => 'application.modules.tags.behaviors.taggable.ETaggableBehavior',
		            'modelTableName' => 'Torrent',
		            'cacheID' => 'cache',
		            'createTagsAutomatically' => true,
		        )
		    )
		);
	}

	protected static function _addUrlRules () {
		/*Yii::app()->pd->addUrlRules(array(
		                                 'tags/<action:\w+>/*' => 'tags/default/<action>',
		                                 'tags/<controller:\w+>/<action:\w+>/*' => 'tags/<controller>/<action>',
		                            ));*/
	}
}
