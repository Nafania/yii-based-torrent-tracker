<?php
/* @var $this modules\blogs\controllers\DefaultController */
/* @var $blog modules\blogs\models\BlogPost */
/* @var $form TbActiveForm */
?>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id' => 'blog-form',
	     'enableAjaxValidation' => true,
	)); ?>

<?php echo $form->textFieldRow($blog, 'title', array('class' => 'span5')); ?>

<?php echo $form->textAreaRow($blog, 'description', array('class' => 'span5')); ?>

	<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType' => 'submit',
		     'type'       => 'primary',
		     'label'      => ($blog->isNewRecord ? Yii::t('blogsModule.common',
			     'Создать блог') : Yii::t('blogsModule.common', 'Сохранить')),
		)); ?>
	</div>

<?php $this->endWidget(); ?>