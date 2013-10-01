<?php
class CommentsRating extends CWidget {
	public $model;

	public function run () {
		Yii::import('application.modules.ratings.models.*');

		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('ratings')->getAssetsUrl() . '/js/ratings.js');

		$this->render('commentsRating',
			array(
			     'modelName' => get_class($this->model),
			     'modelId'   => $this->model->getId(),
			     'rating'    => (int) $this->model->getRating()
			));
	}

	public function getRating () {
		return $this->model->getRating();
	}
}