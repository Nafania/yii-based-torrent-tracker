<?php
class CommentsRating extends CWidget {
	public $model;
	private $_rating;

	public function run () {
		Yii::import('application.modules.ratings.models.*');

		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('ratings')->getAssetsUrl() . '/js/ratings.js');

		if ( $this->model->rating ) {
			$this->_rating = $this->model->rating->getRating();
		}
		else {
			$this->_rating = 0;
		}

		$this->render('commentsRating',
			array(
			     'modelName' => get_class($this->model),
			     'modelId'   => $this->model->getId(),
			     'rating'    => $this->_rating
			));
	}

	public function getRating () {
		return $this->_rating;
	}
}