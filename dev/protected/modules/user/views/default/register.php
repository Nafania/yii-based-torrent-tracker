<?php
/* @var $this DefaultController */
/* @var $model User */
/* @var $form TbActiveForm */
?>


	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		     'id'                     => 'register-form',
		     'enableAjaxValidation'   => true,
		     'enableClientValidation' => true,
		));
	?>

	<?php Yii::app()->getController()->renderPartial('application.modules.user.views.default._register',
				array(
				     'model' => $model,
				     'form' => $form,
				)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => 'Register',
			)); ?>
	</div>

	<?php $this->endWidget(); ?>

