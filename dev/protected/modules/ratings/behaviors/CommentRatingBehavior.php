<?php
class CommentRatingBehavior extends RatingBehavior {
	public $ratingCacheTime = 3600;

	public function calculateRating () {
		$owner = $this->getOwner();
		$ratings = RatingRelations::model()->findAllByAttributes(array('modelName' => get_class($owner), 'modelId' => $owner->primaryKey));

		$ratingVal = 0;
		foreach ( $ratings AS $rating ) {
			$ratingVal += $rating->rating;
		}
		$this->saveRating($ratingVal);
	}
}