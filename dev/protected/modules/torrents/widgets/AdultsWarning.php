<?php
class AdultsWarning extends CWidget {
	public $model;

	public function init() {
		if ( !$this->model instanceof CActiveRecord ) {
			throw new CException('Model must be instance of CActiveRecord');
		}
	}

	public function run() {
		if ( !$this->model->hasTag('18+') || Yii::app()->getRequest()->cookies['AdultsWarning'] ) {
			return false;
		}

		$this->render('adultsWarning');
	}
}