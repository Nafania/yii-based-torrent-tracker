<?php
/* @var $this SiteController */
/* @var $model User */
/* @var $form TbActiveForm  */
?>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'restore-form',
	     'enableAjaxValidation'   => true,
	     'enableClientValidation' => true,
	)); ?>

<?php Yii::app()->getController()->renderPartial('application.modules.user.views.default._restore',
			array(
			     'model' => $model,
			     'form' => $form,
			)); ?>


<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType' => 'submit',
		     'type'       => 'primary',
		     'label'      => Yii::t('userModule.common', 'Restore'),
		)); ?>
</div>

<?php $this->endWidget(); ?>
