<?php
class BlogPostRatingBehavior extends RatingBehavior {
	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * @var modules\blogs\models\BlogPost $owner
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

        $date1 = new DateTime(date('Y-m-d H:i:s', $maxTime));
        $date2 = new DateTime();

        $interval = $date1->diff($date2);

		$ratingVal = Yii::app()->getModule('ratings')->getRatingCoefficient(11) * $owner->commentsCount * exp(Yii::app()->getModule('ratings')->getRatingCoefficient(12) * $interval->h);

		$this->saveRating($ratingVal);
	}
}