<?php
/* @var $this DefaultController */
/* @var $blog BlogPost */
/* @var $form TbActiveForm */
?>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                   => 'blog-form',
	     'enableAjaxValidation' => true,
	)); ?>

<?php echo $form->textFieldRow($blog, 'title', array('class' => 'span5')); ?>

<?php echo $form->textAreaRow($blog, 'description', array('class' => 'span5')); ?>

	<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => ($blog->isNewRecord ? 'Create' : 'Save'),
			)); ?>
	</div>

<?php $this->endWidget(); ?>