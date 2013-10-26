<?php
class GroupOperations extends CWidget {
	public $group;

	public function init () {
		if ( !$this->group instanceof Group ) {
			throw new Exception('Group must be instanceof Group');
		}
	}

	public function run () {
		$cs = Yii::app()->getClientScript();

		$cs->registerScriptFile(Yii::app()->getModule('groups')->getAssetsUrl() . '/js/groups.js');
		$cs->registerScriptFile(Yii::app()->getModule('subscriptions')->getAssetsUrl() . '/js/subscriptions.js');
		$cs->registerScriptFile(Yii::app()->baseUrl . '/js/formValidation.js');
		Yii::app()->getComponent('bootstrap')->registerPackage('loading');


		$this->render('groupOperations',
			array(
			     'group' => $this->group
			));
	}
}