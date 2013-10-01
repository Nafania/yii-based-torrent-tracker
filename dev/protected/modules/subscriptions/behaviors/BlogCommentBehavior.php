<?php
class BlogCommentBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		$owner = $this->getOwner();
		$className = get_class($owner);

		if ( !$owner->modelName == 'BlogPost' ) {
			return false;
		}

		$blogPost = BlogPost::model()->findByPk($owner->modelId);
		if ( !$blogPost ) {
			return false;
		}

		$url = $blogPost->getUrl();
		$icon = $blogPost->getChangesIcon();

		$event = new Event();
		$event->text = $blogPost->getChangesText();
		$event->title = $blogPost->getChangesTitle();
		$event->url = $url;
		$event->icon = $icon;
		$event->uId = $blogPost->ownerId;

		$event->save();

		return true;
	}
}