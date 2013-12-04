<?php
/**
 * @var $form      TbActiveForm
 * @var $model     PrivateMessage
 * @var $models    PrivateMessage[]
 * @var $usersData array
 */
?>

	<h1><?php echo Yii::t('pmsModule.common', 'Создание личного сообщения'); ?></h1>

<?php echo $this->errorSummary($models, array('class' => 'alert alert-block alert-error')); ?>
<?php $this->renderPartial('_form',
	array(
	     'model'     => $model,
	     'usersData' => $usersData
	)); ?>