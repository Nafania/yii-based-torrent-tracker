<?php
class CommentsAnswerBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		$owner = $this->getOwner();
		$className = get_class($owner);

		if ( !($owner instanceof ChangesInterface) ) {
			return true;
		}

		if ( !$owner->getParentId() ) {
			return true;
		}

		$ancestors = $owner->ancestors(10)->findAll();

		$users = array();
		//TODO not all ancestors return
		foreach ( $ancestors AS $comment ) {
			if ( $user = $comment->user ) {
				$users[] = $user;
			}
		}

		$users = array_unique($users);
		$valid = true;
		foreach ( $users AS $user ) {
			if ( $user->getId() == Yii::app()->getUser()->getId() ) {
				continue;
			}

			$url = $owner->getUrl();
			$icon = $owner->getChangesIcon();

			$event = new Event();
			$event->text = $owner->getChangesText();
			$event->title = $owner->getChangesTitle();
			$event->url = $url;
			$event->icon = $icon;
			$event->uId = $user->getId();

			$valid = $event->save() && $valid;
		}

		if ( $valid ) {
			return true;
		}
		else {
			return false;
		}

	}
}