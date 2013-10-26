<?php
/* @var $this DefaultController */
/* @var $groupProvider CActiveDataProvider */
/* @var $model User */
/* @var $group Group */
?>
<?php
Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('groups')->getAssetsUrl() . '/js/groups.js');
?>
<?php
$this->renderPartial('_singleView', array(
                                         'model' => $group,
                                    ))
?>
<?php

if ( Yii::app()->user->checkAccess('updateOwnGroup',
		array('ownerId' => $group->ownerId)) || Yii::app()->user->checkAccess('updateGroup')
) {


	$tabs = array(
		array(
			'label'   => Yii::t('groupsModule.common', 'Участники ({n})', $group->groupUsersCount),
			'content' => $this->widget('zii.widgets.CListView',
				array(
				     'id'           => 'usersListView',
				     'dataProvider' => $model->with('groupUser:new')->search(),
				     'itemView'     => '_member',
				     'viewData'     => array(
					     'group'  => $group,
					     'status' => GroupUser::STATUS_APPROVED,
				     ),
				     'template'     => '{items} {pager}',
				),
				true),
			'url'     => array(
				'/groups/default/members',
				'id' => $group->getId(),
			),
			'active'  => Yii::app()->getRequest()->getUrl() == Yii::app()->createUrl('/groups/default/members',
				array('id' => $group->getId())),
		),
		array(
			'label'   => Yii::t('groupsModule.common', 'Новые пользователи ({n})', $group->newUsersCount),
			'content' => $this->widget('zii.widgets.CListView',
				array(
				     'id'           => 'usersListView',
				     'dataProvider' => $model->with('groupUser:new')->search(),
				     'itemView'     => '_member',
				     'viewData'     => array(
					     'group'  => $group,
					     'status' => GroupUser::STATUS_NEW,
				     ),
				     'template'     => '{items} {pager}',
				),
				true),
			'active'  => Yii::app()->getRequest()->getUrl() == Yii::app()->createUrl('/groups/default/members',
				array(
				     'id'     => $group->getId(),
				     'status' => GroupUser::STATUS_NEW
				)),
			'url'     => Yii::app()->createUrl('/groups/default/members',
				array(
				     'id'     => $group->getId(),
				     'status' => GroupUser::STATUS_NEW
				)),
		),
		array(
			'label'   => Yii::t('groupsModule.common', 'Отклоненные пользователи ({n})', $group->declinedUsersCount),
			'content' => $this->widget('zii.widgets.CListView',
				array(
				     'id'           => 'usersListView',
				     'dataProvider' => $model->with('groupUser:declined')->search(),
				     'itemView'     => '_member',
				     'viewData'     => array(
					     'group'  => $group,
					     'status' => GroupUser::STATUS_DECLINED,
				     ),
				     'template'     => '{items} {pager}',
				),
				true),
			'active'  => Yii::app()->getRequest()->getUrl() == Yii::app()->createUrl('/groups/default/members',
				array(
				     'id'     => $group->getId(),
				     'status' => GroupUser::STATUS_DECLINED
				)),
			'url'     => Yii::app()->createUrl('/groups/default/members',
				array(
				     'id'     => $group->getId(),
				     'status' => GroupUser::STATUS_DECLINED
				)),
			//'url' => Yii::app()->createUrl('/groups/default/members', array('id' => $model->getId(), 'status' => GroupUser::STATUS_DECLINED)),
		),

		array(
			'label'   => Yii::t('groupsModule.common', 'Приглашенные пользователи ({n})', $group->invitedUsersCount),
			'content' => $this->widget('zii.widgets.CListView',
				array(
				     'id'           => 'usersListView',
				     'dataProvider' => $model->with('groupUser:invited')->search(),
				     'itemView'     => '_invitedMember',
				     'viewData'     => array(
					     'group'  => $group,
					     'status' => GroupUser::STATUS_INVITED,
				     ),
				     'template'     => '{items} {pager}',
				),
				true),
			'active'  => Yii::app()->getRequest()->getUrl() == Yii::app()->createUrl('/groups/default/members',
				array(
				     'id'     => $group->getId(),
				     'status' => GroupUser::STATUS_INVITED
				)),
			'url'     => Yii::app()->createUrl('/groups/default/members',
				array(
				     'id'     => $group->getId(),
				     'status' => GroupUser::STATUS_INVITED
				)),
			//'url' => Yii::app()->createUrl('/groups/default/members', array('id' => $model->getId(), 'status' => GroupUser::STATUS_DECLINED)),
		),
	);
	$this->widget('ext.bootstrap.widgets.TbTabs',
		array(
		     'tabs' => $tabs,
		));
}
else {
	$this->widget('zii.widgets.CListView',
		array(
		     'id'           => 'usersListView',
		     'dataProvider' => $model->search(),
		     'itemView'     => '_member',
		     'viewData'     => array(
			     'group'  => $group,
			     'status' => GroupUser::STATUS_APPROVED,
		     ),
		     'template'     => '{items} {pager}',
		));
}
