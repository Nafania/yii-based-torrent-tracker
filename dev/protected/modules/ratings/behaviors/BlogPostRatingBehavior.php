<?php
class BlogPostRatingBehavior extends RatingBehavior {
	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * @var $owner BlogPost
		 */
		/**
		 * будем использовать следующую формулу
		 * K11 * Кол-во комментариев * exp(K12 * (дата последнего комментария не автора - time))
		 */

		$owner = $this->getOwner();

		$comm = Yii::app()->getDb()->createCommand('SELECT MAX(ctime) AS maxTime FROM {{comments}} WHERE modelName = :modelName AND modelId = :modelId AND ownerId != :ownerId');
		$comm->bindValue(':modelName', $owner->resolveClassName());
		$comm->bindValue(':modelId', $owner->getPrimaryKey());
		$comm->bindValue(':ownerId', $owner->ownerId);
		$maxTime = ( $row = $comm->queryRow() ) ? $row['maxTime'] : 0;

		$ratingVal = Yii::app()->getModule('ratings')->getRatingCoefficient(11) * $owner->commentsCount * exp(Yii::app()->getModule('ratings')->getRatingCoefficient(12) * ($maxTime - time()));

		$this->saveRating($ratingVal);
	}
}