<?php
class DeleteCommentsBehavior extends CActiveRecordBehavior {

	public function afterDelete ( $e ) {
		parent::afterDelete($e);

		$owner = $this->getOwner();
		$comments = Comment::model()->findByAttributes(array(
		                                                    'modelName' => get_class($owner),
		                                                    'modelId'   => $owner->primaryKey
		                                               ));

		foreach ( $comments AS $comment ) {
			$comment->delete();
		}

		if ( $count = sizeof($comments) ) {
			$commentCount = CommentCount::model()->findByPk(array(
			                                                     'modelName' => get_class($owner),
			                                                     'modelId'   => $owner->primaryKey
			                                                ));
			if ( !$commentCount ) {
				$commentCount = new CommentCount();
				$commentCount->modelName = $this->modelName;
				$commentCount->modelId = $this->modelId;
			}
			$commentCount->count -= $count;
			$commentCount->save();
		}

		return true;
	}
}