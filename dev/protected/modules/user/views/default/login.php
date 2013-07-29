<?php
/**
 * @var TbActiveForm $form
 */
?>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'login-form',
	     'enableAjaxValidation'   => true,
	     'enableClientValidation' => true,
	)); ?>

<?php Yii::app()->getController()->renderPartial('application.modules.user.views.default._login',
	array('model' => $model, 'form' => $form)); ?>


	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => Yii::t('userModule.common', 'Login'),
			)); ?>

		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'link',
			     'type'       => 'link',
			     'label'      => Yii::t('userModule.common', 'Forgot password'),
			     'url' => Yii::app()->createUrl('/user/default/restore'),
			)); ?>
	</div>

<?php $this->endWidget(); ?>
