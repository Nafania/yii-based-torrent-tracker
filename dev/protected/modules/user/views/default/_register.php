<?php
/* @var $this DefaultController */
/* @var $model User */
/* @var $form TbActiveForm */
?>

	<?php echo $form->textFieldRow($model, 'email', array('class' => 'span5')); ?>

	<?php echo $form->passwordFieldRow($model, 'password', array('class' => 'span5')); ?>
