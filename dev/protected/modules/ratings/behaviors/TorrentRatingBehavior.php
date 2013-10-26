<?php
class TorrentRatingBehavior extends RatingBehavior {
	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * будем использовать следующую формулу
		 * ( (K4 * snatchedCount + K1 * Кол-во комментариев) / К ) * exp(K2 * (mtime - time)) + K3 * сумма рейтингов пользователей;
		 */
		/**
		 * @var $owner TorrentGroup
		 */
		$owner = $this->getOwner();
		$ratings = RatingRelations::model()->findAllByAttributes(array(
		                                                              'modelName' => get_class($owner),
		                                                              'modelId'   => $owner->primaryKey
		                                                         ));

		$sumUserRatings = 0;
		foreach ( $ratings AS $rating ) {
			$sumUserRatings += $rating->rating;
		}
		$ratingVal = ((Yii::app()->getModule('ratings')->getRatingCoefficient(4) * $owner->getDownloadsCount() + Yii::app()->getModule('ratings')->getRatingCoefficient(1) * $owner->commentsCount) / Yii::app()->getModule('ratings')->getRatingCoefficient(0)) * exp(Yii::app()->getModule('ratings')->getRatingCoefficient(2) * ($owner->mtime - time())) + Yii::app()->getModule('ratings')->getRatingCoefficient(3) * $sumUserRatings;

		$this->saveRating($ratingVal);
	}
}