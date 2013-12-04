<?php
class TorrentCommentBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		/**
		 * @var $owner Comment
		 */
		$owner = $this->getOwner();

		/**
		 * если комментарий не новый, или у него нет привязанного торрента или же он оставлен текущим пользователем, то не создаем события
		 */
		if ( !$owner->getIsNewRecord() || !$owner->getTorrentId() || $owner->getTorrent()->uid == Yii::app()->getUser()->getId() ) {
			return true;
		}

		$url = $owner->getUrl();
		$icon = $owner->getChangesIcon();

		$event = new Event();
		$event->text = Yii::t('subscriptionsModule.common',
			'{userName} добавил комментарий к вашему торренту',
			array(
			     '{userName}' => $owner->user->getName()
			));
		$event->title = Yii::t('subscriptionsModule.common', 'Новый комментарий');
		$event->url = $url;
		$event->icon = $icon;
		$event->uId = $owner->getTorrent()->uid;

		$event->save();

		return true;
	}
}