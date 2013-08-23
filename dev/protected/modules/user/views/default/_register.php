<?php
/* @var $this DefaultController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

<?php echo $form->textFieldRow($model, 'email', array('class' => 'span5')); ?>

<?php echo $form->passwordFieldRow($model, 'password', array('class' => 'span5')); ?>

<h4><?php echo Yii::t('userModule.common', 'Регистрация через социальные сети:') ?></h4>

<?php $this->widget('application.modules.user.extensions.eauth.EAuthWidget', array('action' => '/user/default/socialLogin')); ?>
