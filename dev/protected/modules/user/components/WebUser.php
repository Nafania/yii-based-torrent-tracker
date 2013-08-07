<?php

Yii::import('application.modules.auth.components.*');
Yii::import('application.modules.user.models.*');

class WebUser extends AuthWebUser {

	// Store model to not repeat query.
	private $_model;

	public function init() {
		parent::init();
		$this->attachBehaviors($this->behaviors());
	}

	public function behaviors () {
		return Yii::app()->pd->loadBehaviors($this);
	}

	public function getName () {
		return $this->getModel()->name;
	}

	public  function getModel () {
		$id = Yii::app()->user->id;

		if ( $this->_model === null ) {
			if ( $id !== null ) {
				$this->_model = User::model()->findByPk($id);
			}
		}
		return $this->_model;
	}

	public function getProfile () {
		return $this->getModel()->profile;
	}
}