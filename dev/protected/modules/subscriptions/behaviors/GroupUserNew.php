<?php

/**
 * Class GroupUserNew
 * Поведение для оповещения владельца группы о вступлении в нее участников
 */
class GroupUserNew extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		/**
		 * @var $owner GroupUser
		 */
		$owner = $this->getOwner();

		if ( $owner->getIsNewRecord() && $owner->status == GroupUser::STATUS_NEW ) {
			$group = $owner->group;

			$url = Yii::app()->createUrl('/groups/default/members',
				array(
					'id' => $group->getId(),
					'status' => GroupUser::STATUS_NEW
				));
			$icon = 'user';

			$event = new Event();
			$event->text = Yii::t('groupsModule.common',
				'В вашу группу "{title}" вступил новый участник.',
				array(
					'{title}' => $group->getTitle()
				));
			$event->title = Yii::t('groupsModule.common', 'Новый участник в группе');
			$event->url = $url;
			$event->icon = $icon;
			$event->uId = $group->ownerId;

			return $event->save();
		}

		return true;
	}
}