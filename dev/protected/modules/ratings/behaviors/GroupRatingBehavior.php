<?php
class GroupRatingBehavior extends RatingBehavior {
	/**
	 * @var $owner Group
	 */

	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * будем использовать следующую формулу
		 * K15 * Сумма рейтингов всех постов * exp(K16 * (дата последней записи - time))
		 */

		$owner = $this->getOwner();

		$comm = Yii::app()->getDb()->createCommand('SELECT MAX(ctime) AS maxTime FROM {{blogPosts}} WHERE blogId = :blogId');
		$comm->bindValue(':blogId', $owner->getPrimaryKey());
		$maxTime = ( $row = $comm->queryRow() ) ? $row['maxTime'] : 0;

		$comm = Yii::app()->getDb()->createCommand('SELECT SUM(rating) AS rating FROM {{ratings}} r LEFT JOIN {{blogPosts}} bb ON ( r.modelName = :modelName AND r.modelId = bb.id), blogs b WHERE bb.blogId = b.id AND b.groupId = :groupId');
		$comm->bindValue(':modelName', 'modules_blogs_models_BlogPost');
		$comm->bindValue(':groupId', $owner->getPrimaryKey());
		$sumRatings = ( $row = $comm->queryRow() ) ? $row['rating'] : 0;

        $date1 = new DateTime(date('Y-m-d H:i:s', $maxTime));
        $date2 = new DateTime();

        $interval = $date1->diff($date2);

		$ratingVal = Yii::app()->getModule('ratings')->getRatingCoefficient(15) * $sumRatings * exp(Yii::app()->getModule('ratings')->getRatingCoefficient(16) * $interval->h);

		$this->saveRating($ratingVal);
	}
}