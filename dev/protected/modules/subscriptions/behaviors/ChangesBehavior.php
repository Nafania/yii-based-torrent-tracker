<?php
class ChangesBehavior extends CActiveRecordBehavior {
	public function beforeSave ( $e ) {
		parent::beforeSave($e);

		$owner = $this->getOwner();
		$className = get_class($owner);

		if ( !($owner instanceof ChangesInterface) || $owner->getIsNewRecord() ) {
			return true;
		}

		$oldModel = $className::model()->findByPk($owner->getPrimaryKey());

		if ( $owner->getMtime() <= $oldModel->getMtime() ) {
			return true;
		}

		$subscriptions = Subscription::model()->findAllByAttributes(array(
		                                                                 'modelId'   => $owner->getPrimaryKey(),
		                                                                 'modelName' => $className,
		                                                            ));

		$valid = true;
		foreach ( $subscriptions AS $subscription ) {
			$url = $owner->getUrl();
			$icon = $owner->getChangesIcon();

			$event = new Event();
			$event->text = $owner->getChangesText();
			$event->title = $owner->getChangesTitle();
			$event->url = $url;
			$event->icon = $icon;
			$event->uId = $subscription->uId;

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