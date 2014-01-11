<?php
class BlogRatingBehavior extends RatingBehavior {
	/**
	 * @var $owner modules\blogs\models\Blog
	 */

	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * будем использовать следующую формулу
		 * K13 * Сумма рейтингов всех постов * exp(K14 * (дата последней записи - time))
		 */

		$owner = $this->getOwner();

		$comm = Yii::app()->getDb()->createCommand('SELECT MAX(ctime) AS maxTime FROM {{blogPosts}} WHERE blogId = :blogId');
		$comm->bindValue(':blogId', $owner->getPrimaryKey());
		$maxTime = ( $row = $comm->queryRow() ) ? $row['maxTime'] : 0;

		$comm = Yii::app()->getDb()->createCommand('SELECT SUM(rating) AS rating FROM {{ratings}} r LEFT JOIN {{blogPosts}} b ON ( r.modelName = :modelName AND r.modelId = b.id) WHERE b.blogId = :blogId');
		$comm->bindValue(':modelName', 'modules_blogs_models_BlogPost');
		$comm->bindValue(':blogId', $owner->getPrimaryKey());
		$sumRatings = ( $row = $comm->queryRow() ) ? $row['rating'] : 0;

		$ratingVal = Yii::app()->getModule('ratings')->getRatingCoefficient(13) * $sumRatings * exp(Yii::app()->getModule('ratings')->getRatingCoefficient(14) * ($maxTime - time()));

		$this->saveRating($ratingVal);
	}
}