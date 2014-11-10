<?php
/* @var $this AssignmentController */
/* @var $dataProvider CActiveDataProvider */
?>
<h1><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView',
	array(
		//'type' => 'striped hover',
		'id'            => 'objects-grid',
		'enableHistory' => true,
		'filter'        => $model,
		'dataProvider'  => $model->search(),
		'emptyText'     => Yii::t('AuthModule.main', 'No assignments found.'),
		'columns'       => array(
			array(
				'name'  => 'name',
				'type'  => 'html',
				'value' => 'CHtml::link(CHtml::value($data, Yii::app()->getModule("auth")->userNameColumn),
							array(
								"view",
								"id" => $data->{Yii::app()->getModule("auth")->userIdColumn}
							))',
			),
			array(
				'header' => Yii::t('AuthModule.main', 'Assigned items'),
				'class'  => 'AuthAssignmentItemsColumn',
			),
			array(
				'class'                => 'YiiAdminButtonColumn',
				'deleteButtonOptions'    => array('style' => 'display:none;',),
				'updateButtonOptions'    => array('style' => 'display:none;',),
				'viewButtonUrl'        => "Yii::app()->createUrl('/auth/assignment/view', array('id' => \$data->{Yii::app()->getModule('auth')->userIdColumn}))"
			),
		),
	)); ?>
