<?php
/* @var $this DefaultController */
/* @var $model Group */
/* @var $form TbActiveForm */
?>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id' => 'group-form',
	     'enableAjaxValidation' => true,
	     'htmlOptions' => array(
		     'enctype' => 'multipart/form-data'
	     )
	)); ?>

<?php echo $form->fileFieldRow($model, 'picture', array('class' => 'span5')); ?>
<?php echo $form->textFieldRow($model, 'title', array('class' => 'span5')); ?>
<div>
<?php
	echo $form->labelEx($model,
		'type',
		array(
		     'data-toggle'         => 'tooltip',
		     'data-placement'      => 'right',
		     'title' => Yii::t('torrentsModule.common',
			     'В открытую группу может вступить любой человек, в закрытую же только по приглашению владельца группы.'),
		     'class'               => 'attributeDescription',
		));
	?>
</div>
<?php echo $form->dropDownList($model, 'type', $model->getTypes(), array('class' => 'span5')); ?>

<?php echo $form->textAreaRow($model, 'description', array('class' => 'span5')); ?>

	<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType' => 'submit',
		     'type'       => 'primary',
		     'label'      => ($model->isNewRecord ? Yii::t('groupsModule.common',
			     'Создать') : Yii::t('groupsModule.common', 'Сохранить')),
		)); ?>
	</div>

<?php $this->endWidget(); ?>