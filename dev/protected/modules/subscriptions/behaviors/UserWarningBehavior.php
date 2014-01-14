<?php
class UserWarningBehavior extends CActiveRecordBehavior {
	/**
	 * @method modules\userwarnings\models\UserWarning getOwner()
	 */

	public function afterSave($e) {
		parent::afterSave($e);

		/**
		 * @var modules\userwarnings\models\UserWarning $owner
		 */
		$owner = $this->getOwner();

		if ( $owner->getIsNewRecord() ) {
			$url = $owner->user->getUrl();
			$icon = 'warning-sign';

			$event = new Event();
			$event->text = $owner->getFullText(false);
			$event->title = Yii::t('userwarningsModule.common', 'Вы получили предупреждение');
			$event->url = $url;
			$event->icon = $icon;
			$event->uId = $owner->uId;

			return $event->save();
		}

		return true;
	}
}