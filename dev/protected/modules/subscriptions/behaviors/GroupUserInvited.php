<?php
/**
 * Class GroupUserInvited
 * Поведение для оповещения о приглашении пользователя в группу
 */
class GroupUserInvited extends CActiveRecordBehavior {
	public function afterSave($e) {
		parent::afterSave($e);

		/**
		 * @var $owner GroupUser
		 */
		$owner = $this->getOwner();

		if ( $owner->status != GroupUser::STATUS_INVITED ) {
			return true;
		}

		$url = array('/groups/default/my');
		$icon = 'user';

		$event = new Event();
		$event->text = Yii::t('groupsModule.common',
			'Вы были приглашены в группу "{title}"',
			array(
			     '{title}' => $owner->group->getTitle()
			));
		$event->title = Yii::t('groupsModule.common', 'Приглашение в группу');
		$event->url = $url;
		$event->icon = $icon;
		$event->uId = $owner->idUser;

		return $event->save();
	}
}