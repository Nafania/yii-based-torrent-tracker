<?php
class BlogPostRatingBehavior extends RatingBehavior {
	/**
	 * @var $owner BlogPost
	 */

	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * будем использовать следующую формулу
		 * K11 * Кол-во комментариев * exp(K12 * (дата последнего комментария - time))
		 */

		$owner = $this->getOwner();

		$comm = Yii::app()->getDb()->createCommand('SELECT MAX(ctime) AS maxTime FROM {{comments}} WHERE modelName = :modelName AND modelId = :modelId');
		$comm->bindValue(':modelName', get_class($owner));
		$comm->bindValue(':modelId', $owner->getPrimaryKey());
		$maxTime = ( $row = $comm->queryRow() ) ? $row['maxTime'] : 0;

		$ratingVal = Yii::app()->getModule('ratings')->getRatingCoefficient(11) * $owner->commentsCount * exp(Yii::app()->getModule('ratings')->getRatingCoefficient(12) * ($maxTime - time()));

		$this->saveRating($ratingVal);
	}
}