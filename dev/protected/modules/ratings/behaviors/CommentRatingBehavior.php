<?php
class CommentRatingBehavior extends RatingBehavior {
	public $ratingCacheTime = 3600;

	public function calculateRating () {
		$owner = $this->getOwner();
		$ratings = RatingRelations::model()->findAllByAttributes(array('modelName' => $owner->resolveClassName(), 'modelId' => $owner->primaryKey));

		$ratingVal = 0;
		foreach ( $ratings AS $rating ) {
			$ratingVal += $rating->rating;
		}
		$this->saveRating($ratingVal);
	}
}