<?php
/*
 * @var $model StaticPage
 */
?>
	<h1><?php echo Yii::t('userModule.common', 'Управление пользователями'); ?></h1>
	<ul class="tools">
    <li> 
       <?php echo CHtml::link(Yii::t('userModule.common', 'Создать группу'),
	       $this->createUrl('/groups/groupsBackend/create'),
	       array('class' => 'add-handler focus')); ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
		'id'           => 'objects-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUrl'      => Yii::app()->createUrl('/user/usersBackend/index'),
		'columns'      => array(
			'id',
			'email',
			'name',
			array(
				'class'        => 'DToggleColumn',
				'name'         => 'active',
				'confirmation' => Yii::t('userModule.common', 'Изменить блокировку?'),
				'linkUrl'      => Yii::app()->createUrl('/user/usersBackend/toggleBan'),
				'filter'       => array(
					User::USER_ACTIVE     => Yii::t('userModule.common', 'Active'),
					User::USER_NOT_ACTIVE => Yii::t('userModule.common', 'Not active')
				),
			),
			'ctime',
			array(
				'class'                => 'YiiAdminButtonColumn',
				'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
				'updateButtonUrl'      => "Yii::app()->createUrl('/groups/groupsBackend/update', array('id' => \$data->getId()))",

				'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
				'deleteButtonUrl'      => "Yii::app()->createUrl('/groups/groupsBackend/delete', array('id' => \$data->getId()))",
				'viewButtonOptions'    => array('style' => 'display:none;',),
			),
		),
	));
?>