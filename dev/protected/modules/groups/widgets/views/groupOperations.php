<?php
/**
 * @var $group Group
 */
?>

<div class="groupOperations">
<?php
if ( Yii::app()->user->checkAccess('createPostInGroupMemberBlog',
		array('isMember' => Group::checkJoin($group))) || Yii::app()->user->checkAccess('createPostInGroup')
) {
	$this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'link',
		     'icon'        => 'pencil',
		     'url'         => array(
			     '/blogs/post/create',
			     'blogId' => $group->blog->getId()
		     ),
		     'htmlOptions' => array(
			     'data-toggle'         => 'tooltip',
			     'data-placement'      => 'top',
			     'title' => Yii::t('blogsModule.common', 'Создать запись в этой группе'),
		     )
		));
}
?>

<?php
if ( Subscription::check($group) ) {
	$this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'link',
		     'icon'        => 'eye-close',
		     'url'         => array('/subscriptions/default/delete'),
		     'htmlOptions' => array(
			     'data-toggle'         => 'tooltip',
			     'data-placement'      => 'top',
			     'data-model'          => $group->resolveClassName(),
			     'data-id'             => $group->getId(),
			     'data-action'         => 'subscription',
			     'title' => Yii::t('groupsModule.common',
				     'Перестать следить за этой группой')
		     ),
		     'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.delete'),
		));
}
else {
	$this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'link',
		     'icon'        => 'eye-open',
		     'url'         => array('/subscriptions/default/create'),
		     'htmlOptions' => array(
			     'data-toggle'         => 'tooltip',
			     'data-placement'      => 'top',
			     'data-model'          => $group->resolveClassName(),
			     'data-id'             => $group->getId(),
			     'data-action'         => 'subscription',
			     'title' => Yii::t('groupsModule.common',
				     'Следить за этой группой')
		     ),
		     'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.create'),
		));
}
?>

<?php
if ( Yii::app()->user->checkAccess('unJoinGroup',
	array(
	     'ownerId' => $group->ownerId,
	     'isMember' => Group::checkJoin($group),
	))
) {
	$this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'link',
		     'label'       => Yii::t('groupsModule.common', 'Выйти из группы'),
		     'url'         => array(
			     '/groups/default/unJoin',
			     'id' => $group->getId()
		     ),
		     'htmlOptions' => array(
			     'data-action'       => 'join',
			     'data-loading-text' => Yii::t('groupsModule.common', 'Идет отправка запроса'),
		     )
		));
}
?>

<?php
if ( Yii::app()->user->checkAccess('joinGroup',
	array(
	     'ownerId' => $group->ownerId,
	     'isMember'  => Group::checkJoin($group),
	     'groupType' => $group->getType()
	))
) {
	$this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'link',
		     'label'       => Yii::t('groupsModule.common', 'Вступить в группу'),
		     'url'         => array(
			     '/groups/default/join',
			     'id' => $group->getId()
		     ),
		     'htmlOptions' => array(
			     'data-action'       => 'join',
			     'data-loading-text' => Yii::t('groupsModule.common', 'Идет отправка запроса'),
		     )
		));
}

if ( Yii::app()->user->checkAccess('updateMembersStatusInOwnGroup',
		array('ownerId' => $group->ownerId)) || Yii::app()->user->checkAccess('updateGroup')
) {
	?>

	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'link',
		     'type'        => 'info',
		     'icon'        => 'user',
		     'url'         => array(
			     '/groups/default/invite',
			     'id' => $group->getId()
		     ),
		     'htmlOptions' => array(
			     'data-action'         => 'groupInvite',
			     'data-toggle'         => 'tooltip',
			     'data-placement'      => 'top',
			     'title' => Yii::t('groupsModule.common', 'Пригласить пользователей в группу'),
		     )
		));
}
if ( Yii::app()->user->checkAccess('updateOwnGroup',
		array('ownerId' => $group->ownerId)) || Yii::app()->user->checkAccess('updateGroup')
) {
	?>

	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'submitLink',
		     'type'        => 'success',
		     'icon'        => 'edit',
		     'url'         => array(
			     '/groups/default/update',
			     'id' => $group->getId()
		     ),
		     'htmlOptions' => array(
			     'csrf'                => true,
			     'href'                => array(
				     '/groups/default/update',
				     'id' => $group->getId()
			     ),
			     'data-toggle'         => 'tooltip',
			     'data-placement'      => 'top',
			     'title' => Yii::t('groupsModule.common', 'Редактировать группу'),
		     )
		));
	?>
<?php } ?>

<?php
if ( Yii::app()->user->checkAccess('deleteOwnGroup',
		array('ownerId' => $group->ownerId)) || Yii::app()->user->checkAccess('deleteGroup')
) {
	?>
	<?php
	$this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'link',
		     'type'        => 'danger',
		     'icon'        => 'trash',
		     'url'         => array(
			     '/groups/default/delete',
			     'id' => $group->getId()
		     ),
		     'htmlOptions' => array(
			     'csrf'                => true,
			     'href'                => array(
				     '/groups/default/update',
				     'id' => $group->getId()
			     ),
			     'data-toggle'         => 'tooltip',
			     'data-placement'      => 'top',
			     'title' => Yii::t('groupsModule.common', 'Удалить группу'),
		     )
		));
	?>
<?php } ?>
</div>