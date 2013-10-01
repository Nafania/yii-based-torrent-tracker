<?php
/* @var $this AssignmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    Yii::t('AuthModule.main', 'Assignments'),
);
$this->widget('zii.widgets.CMenu', array(
		//'type' => 'tabs',
		'items' => array(
			array(
				'label' => Yii::t('AuthModule.main', 'Assignments'),
				'url' => array('/auth/assignment/index'),
				'active' => $this instanceof AssignmentController,
			),
			array(
				'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_ROLE, true)),
				'url' => array('/auth/role/index'),
				'active' => $this instanceof RoleController,
			),
			array(
				'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_TASK, true)),
				'url' => array('/auth/task/index'),
				'active' => $this instanceof TaskController,
			),
			array(
				'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_OPERATION, true)),
				'url' => array('/auth/operation/index'),
				'active' => $this instanceof OperationController,
			),
		),
	));?>

<h1><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    //'type' => 'striped hover',
    'dataProvider' => $dataProvider,
	'emptyText' => Yii::t('AuthModule.main', 'No assignments found.'),
	'template'=>"{items}\n{pager}",
    'columns' => array(
        array(
            'header' => Yii::t('AuthModule.main', 'User'),
            'class' => 'AuthAssignmentNameColumn',
        ),
        array(
            'header' => Yii::t('AuthModule.main', 'Assigned items'),
            'class' => 'AuthAssignmentItemsColumn',
        ),
        array(
            'class' => 'AuthAssignmentViewColumn',
        ),
    ),
)); ?>
