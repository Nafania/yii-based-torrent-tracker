<?php
class PmBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		/**
		 * @var $owner PrivateMessage
		 */
		$owner = $this->getOwner();

		if ( !$owner->getIsNewRecord() ) {
			return true;
		}

		$url = $owner->getUrl();
		$icon = 'envelope';

		$event = new Event();
		$event->text = Yii::t('subscriptionsModule.common',
			'{userName} послал вам новое личное сообщение',
			array(
			     '{userName}' => $owner->user->getName()
			));
		$event->title = Yii::t('subscriptionsModule.common', 'Новое личное сообщение');
		$event->url = $url;
		$event->icon = $icon;
		$event->uId = $owner->receiverUid;

		$event->save();

		return true;
	}
}