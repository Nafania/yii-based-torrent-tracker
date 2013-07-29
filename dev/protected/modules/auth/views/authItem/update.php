<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $model AuthItemForm */
/* @var $item CAuthItem */
/* @var $form TbActiveForm */

$this->breadcrumbs = array(
	$this->capitalize($this->getTypeText(true)) => array('index'),
	$item->description => array('view', 'name' => $item->name),
	Yii::t('AuthModule.main', 'Edit'),
);
?>

<h1>
	<?php echo CHtml::encode($item->description); ?>
	<small><?php echo $this->getTypeText(); ?></small>
</h1>

<?php $form = $this->beginWidget('CActiveForm', array(
	//'type'=>'horizontal',
)); ?>

<?php echo $form->hiddenField($model, 'type'); ?>
<?php
echo $form->label($model, 'name');
echo $form->textField($model, 'name', array(
	'disabled'=>true,
	'title'=>Yii::t('AuthModule.main', 'System name cannot be changed after creation.'),
)); ?>
<?php
echo $form->label($model, 'description');
echo $form->textField($model, 'description');
?>

<div class="form-actions">
	<?php echo CHtml::submitButton(Yii::t('AuthModule.main', 'Save')) ?>

	<?php echo CHtml::link(Yii::t('AuthModule.main', 'Cancel'), array('view', 'name' => $item->name)) ?>
</div>

<?php $this->endWidget(); ?>