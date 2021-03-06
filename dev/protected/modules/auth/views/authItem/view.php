<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $item CAuthItem */
/* @var $ancestorDp AuthItemDataProvider */
/* @var $descendantDp AuthItemDataProvider */
/* @var $formModel AddAuthItemForm */
/* @var $form TbActiveForm */
/* @var $childOptions array */
?>

<div class="title-row clearfix">

	<h1 class="pull-left">
		<?php echo CHtml::encode($item->description); ?>
		<small><?php echo $this->getTypeText(); ?></small>
	</h1>

	<?php echo CHtml::link(Yii::t('AuthModule.main', 'Edit'),array('update', 'name'=>$item->name)); ?>
	<?php echo CHtml::link(Yii::t('AuthModule.main', 'Delete'),array('delete', 'name'=>$item->name), array(
						'confirm'=>Yii::t('AuthModule.main', 'Are you sure you want to delete this item?'),
					)
				); ?>
</div>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data' => $item,
    'attributes' => array(
        array(
            'name' => 'name',
			'label' => Yii::t('AuthModule.main', 'System name'),
		),
		array(
			'name' => 'description',
			'label' => Yii::t('AuthModule.main', 'Description'),
		),

        array(
			'name' => 'bizrule',
			'label' => Yii::t('AuthModule.main', 'Business rule'),
		),
		array(
			'name' => 'data',
			'label' => Yii::t('AuthModule.main', 'Data'),
		),
    ),
)); ?>

<hr />

<div class="row">

	<div class="span6">

		<h3>
			<?php echo Yii::t('AuthModule.main', 'Ancestors'); ?>
			<small><?php echo Yii::t('AuthModule.main', 'Permissions that inherit this item'); ?></small>
		</h3>

		<?php $this->widget('zii.widgets.grid.CGridView', array(
			//'type' => 'striped condensed hover',
			'dataProvider'=>$ancestorDp,
			'emptyText'=>Yii::t('AuthModule.main', 'This item does not have any ancestors.'),
			'template'=>"{items}",
			'hideHeader'=>true,
			'columns'=>array(
				array(
					'class'=>'AuthItemDescriptionColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemTypeColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemRemoveColumn',
					'itemName'=>$item->name,
				),
			),
		)); ?>

	</div>

	<div class="span6">

		<h3>
			<?php echo Yii::t('AuthModule.main', 'Descendants'); ?>
			<small><?php echo Yii::t('AuthModule.main', 'Permissions granted by this item'); ?></small>
		</h3>

		<?php $this->widget('zii.widgets.grid.CGridView', array(
			//'type' => 'striped condensed hover',
			'dataProvider'=>$descendantDp,
			'emptyText'=>Yii::t('AuthModule.main', 'This item does not have any descendants.'),
			'hideHeader'=>true,
			'template'=>"{items}",
			'columns'=>array(
				array(
					'class'=>'AuthItemDescriptionColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemTypeColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemRemoveColumn',
					'itemName'=>$item->name,
				),
			),
		)); ?>

	</div>

</div>

<div class="row">

	<div class="span6 offset6">

		<?php if (!empty($childOptions)): ?>

			<h4><?php echo Yii::t('AuthModule.main', 'Add child'); ?></h4>

			<?php $form = $this->beginWidget('CActiveForm', array(
				//'type'=>'inline',
			)); ?>

			<?php echo $form->dropDownList($formModel, 'items', $childOptions, array('label'=>false)); ?>

		<?php echo CHtml::submitButton(Yii::t('AuthModule.main', 'Add')) ?>

			<?php $this->endWidget(); ?>

		<?php endif; ?>

	</div>

</div>