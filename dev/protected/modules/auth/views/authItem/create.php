<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $model AuthItemForm */
/* @var $form TbActiveForm */

$this->breadcrumbs = array(
	$this->capitalize($this->getTypeText(true)) => array('index'),
	Yii::t('AuthModule.main', 'New {type}', array('{type}' => $this->getTypeText())),
);
?>

<h1><?php echo Yii::t('AuthModule.main', 'New {type}', array('{type}' => $this->getTypeText())); ?></h1>

<?php $form = $this->beginWidget('CActiveForm', array(
	//'type'=>'horizontal',
)); ?>

<?php echo $form->hiddenField($model, 'type'); ?>
<?php
echo $form->label($model, 'name');
echo $form->textField($model, 'name'); ?>

<?php echo $form->label($model, 'description');
echo $form->textField($model, 'description'); ?>

<div class="form-actions">
	<?php echo CHtml::submitButton(Yii::t('AuthModule.main', 'Create')); ?>
	<?php echo CHtml::link(Yii::t('AuthModule.main', 'Cancel'), array('index')); ?>
</div>

<?php $this->endWidget(); ?>
