<?php
class TorrentGroupRating extends CWidget {
	public $model;
	private $_rating;

	public function run () {
		Yii::import('application.modules.ratings.models.*');

		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('ratings')->getAssetsUrl() . '/js/ratings.js');
		Yii::app()->getComponent('bootstrap')->registerPackage('loading');

		$modelName = get_class($this->model);
		$modelId = $this->model->getId();

		if ( $this->model->rating ) {
			$this->_rating = $this->model->rating->getRating();

			$positiveRating = RatingRelations::model()->countByAttributes(array(
			                                                                   'modelName' => $modelName,
			                                                                   'modelId'   => $modelId,
			                                                                   'state'     => RatingRelations::RATING_STATE_PLUS
			                                                              ));
			$negativeRating = abs($this->_rating - $positiveRating);

			$positivePercents = 100 / ( $positiveRating + $negativeRating ) * $positiveRating;
			$negativePercents = 100 - $positivePercents;
		}
		else {
			$this->_rating = $positiveRating = $negativeRating = $negativePercents = $positivePercents = 0;
		}

		$this->render('torrentGroupRating',
			array(
			     'modelName'      => $modelName,
			     'modelId'        => $modelId,
			     'rating'         => $this->_rating,
			     'positiveRating' => $positiveRating,
			     'negativeRating' => $negativeRating,
			     'positivePercents'    => $positivePercents,
			     'negativePercents'    => $negativePercents,
			));
	}

	public function getRating () {
		return $this->_rating;
	}
}