<?php

class WarningButton extends CWidget {
	public $model;

	public function init () {
		parent::init();

		if ( !($this->model instanceof User) ) {
			throw new CException('Model must be instance if User');
		}
	}

	public function run () {
		if ( !Yii::app()->getUser()->checkAccess('userwarnings.default.create') || !Yii::app()->getUser()->checkAccess('createUserWarning',
				array('model' => $this->model))
		) {
			return false;
		}

		$this->render('warningButton',
			array(
				'model' => $this->model
			));
	}
}