<?php
class TorrentRatingBehavior extends RatingBehavior {
	public $ratingCacheTime = 3600;

	public function calculateRating () {
		/**
		 * будем использовать следующую формулу
		 * ( (K4 * snatchedCount) / К ) * exp(K2 * ($lastCommentTime - time)) + K3 * сумма рейтингов пользователей + K1 * Кол-во комментариев;
		 */
		/**
		 * @var $owner modules\torrents\models\TorrentGroup
		 */
		$owner = $this->getOwner();

		$db = $owner->getDbConnection();
		$sql = 'SELECT SUM(rating) AS rating FROM {{ratingRelations}} WHERE modelName = :modelName AND modelId = :modelId';
		$comm = $db->createCommand($sql);
		$comm->bindValue(':modelName', $owner->resolveClassName());
		$comm->bindValue(':modelId', $owner->getPrimaryKey());
		$sumUserRatings = ($row = $comm->queryRow()) ? $row['rating'] : 0;

		$sql = 'SELECT MAX(ctime) AS ctime FROM {{comments}} WHERE modelName = :modelName AND modelId = :modelId';
		$comm = $db->createCommand($sql);
		$comm->bindValue(':modelName', $owner->resolveClassName());
		$comm->bindValue(':modelId', $owner->getPrimaryKey());
		$lastCommentTime = ($row = $comm->queryRow()) ? $row['ctime'] : 0;

		$ratingVal = ((Yii::app()->getModule('ratings')->getRatingCoefficient(4) * $owner->getDownloadsCount()) / Yii::app()->getModule('ratings')->getRatingCoefficient(0)) * exp(Yii::app()->getModule('ratings')->getRatingCoefficient(2) * ($lastCommentTime - time())) + Yii::app()->getModule('ratings')->getRatingCoefficient(3) * $sumUserRatings + Yii::app()->getModule('ratings')->getRatingCoefficient(1) * $owner->commentsCount;

		$this->saveRating($ratingVal);
	}
}