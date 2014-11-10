<?php
/*
 * @var $model StaticPage
 */
?>
	<h1><?php echo Yii::t('GroupsModule.common', 'Управление группами'); ?></h1>
	<ul class="tools">
    <li> 
       <?php echo CHtml::link(Yii::t('GroupsModule.common', 'Создать группу'),
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
	     'ajaxUrl'      => Yii::app()->createUrl('/groups/groupsBackend/index'),
	     'columns'      => array(
		     'id',
		     'title',
		     array(
			     'class'        => 'DToggleColumn',
			     'name'         => 'blocked',
			     'confirmation' => Yii::t('GroupsModule.common', 'Изменить блокировку?'),
			     'linkUrl' => Yii::app()->createUrl('actions/actionsBackend/toggle'),
			     'filter'       => array(
				     Group::BLOCKED     => Yii::t('GroupsModule.common', 'Blocked'),
				     Group::NOT_BLOCKED => Yii::t('GroupsModule.common', 'Not blocked')
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