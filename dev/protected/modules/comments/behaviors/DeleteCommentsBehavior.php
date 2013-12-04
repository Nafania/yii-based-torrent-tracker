<?php
class DeleteCommentsBehavior extends CActiveRecordBehavior {

	public function afterDelete ( $e ) {
		parent::afterDelete($e);

		$owner = $this->getOwner();
		$comments = Comment::model()->findAllByAttributes(array(
		                                                    'modelName' => $owner->resolveClassName(),
		                                                    'modelId'   => $owner->primaryKey
		                                               ));

		foreach ( $comments AS $comment ) {
			$comment->delete();
		}

		if ( $count = sizeof($comments) ) {
			$commentCount = CommentCount::model()->findByPk(array(
			                                                     'modelName' => $owner->resolveClassName(),
			                                                     'modelId'   => $owner->primaryKey
			                                                ));
			if ( !$commentCount ) {
				$commentCount = new CommentCount();
				$commentCount->modelName = self::classNameToNamespace($this->modelName);
				$commentCount->modelId = $this->modelId;
			}
			$commentCount->count -= $count;
			$commentCount->save();
		}

		return true;
	}
}