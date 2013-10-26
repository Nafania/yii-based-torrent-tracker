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

		$positiveRating = $negativeRating = 0;

		$ratings = RatingRelations::model()->findAllByAttributes(array(
		                                                              'modelName' => $modelName,
		                                                              'modelId'   => $modelId,
		                                                         ));
		foreach ( $ratings AS $rating ) {
			if ( $rating->state == RatingRelations::RATING_STATE_PLUS ) {
				$positiveRating += $rating->rating;
			}
			else {
				$negativeRating += abs($rating->rating);
			}
		}

		if ( $positiveRating || $negativeRating ) {
			$positivePercents = 100 / ($positiveRating + $negativeRating) * $positiveRating;
			$negativePercents = 100 - $positivePercents;
		}
		else {
			$positivePercents = $negativePercents = 0;
		}

		$this->render('torrentGroupRating',
			array(
			     'modelName'        => $modelName,
			     'modelId'          => $modelId,
			     'rating'           => $this->_rating,
			     'positiveRating'   => $positiveRating,
			     'negativeRating'   => $negativeRating,
			     'positivePercents' => $positivePercents,
			     'negativePercents' => $negativePercents,
			));
	}

	public function getRating () {
		return $this->_rating;
	}
}