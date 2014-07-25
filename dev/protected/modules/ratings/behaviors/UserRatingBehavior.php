<?php
class UserRatingBehavior extends RatingBehavior {
	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * будем использовать следующую формулу
		 * ( K5 * Кол-во торрентов + K6 * кол-во комментариев + К7 * кол-во записей + K8 * сумма скачиваний торрентов  + K9 * сумма рейтингов комментариев + К10 * сумма рейтингов записей в блоге / К )  K17 * сумма предупреждений;
		 */
		/**
		 * @var $owner User
		 */
		$owner = $this->getOwner();

		$db = Yii::app()->getDb();

		$comm = $db->createCommand('SELECT SUM(downloads) AS rating FROM {{torrents}} t WHERE t.uId = :uId');
		$comm->bindValue(':uId', $owner->getId());
		$sumTorrentRatings = ($row = $comm->queryRow()) ? $row['rating'] : 0;

		$comm = $db->createCommand('SELECT SUM(rating) AS rating FROM {{ratings}} r, {{comments}} t WHERE r.modelId = t.id AND r.modelName = :modelName AND r.modelName = t.modelName AND t.ownerId = :uId');
		$comm->bindValue(':modelName', 'Comment');
		$comm->bindValue(':uId', $owner->getId());
		$sumCommentRatings = ($row = $comm->queryRow()) ? $row['rating'] : 0;

		$comm = $db->createCommand('SELECT SUM(rating) AS rating FROM {{ratings}} r, {{blogPosts}} t WHERE r.modelId = t.id AND r.modelName = :modelName AND t.ownerId = :uId');
		$comm->bindValue(':modelName', 'modules_blogs_models_BlogPost');
		$comm->bindValue(':uId', $owner->getId());
		$sumBlogPostsRatings = ($row = $comm->queryRow()) ? $row['rating'] : 0;

		$comm = $db->createCommand('SELECT COUNT(*) AS count FROM {{userWarnings}} WHERE uId = :uId');
		$comm->bindValue(':uId', $owner->getId());
		$warningsCount = ($row = $comm->queryRow()) ? $row['count'] : 0;

		$ratingVal = ((
				Yii::app()->getModule('ratings')->getRatingCoefficient(5) * $owner->torrentsCount +
				Yii::app()->getModule('ratings')->getRatingCoefficient(6) * $owner->commentsCount +
				Yii::app()->getModule('ratings')->getRatingCoefficient(8) * $sumTorrentRatings +
				Yii::app()->getModule('ratings')->getRatingCoefficient(7) * $owner->blogPostsCount +
				Yii::app()->getModule('ratings')->getRatingCoefficient(9) * $sumCommentRatings +
				Yii::app()->getModule('ratings')->getRatingCoefficient(10) * $sumBlogPostsRatings) /
			Yii::app()->getModule('ratings')->getRatingCoefficient(0)) -
			Yii::app()->getModule('ratings')->getRatingCoefficient(17) * $warningsCount
		;
		$this->saveRating($ratingVal);
	}
}