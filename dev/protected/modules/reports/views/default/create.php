<?php if ( !Yii::app()->getUser()->checkAccess('reports.default.create') ) {
	return;
}
?>
<?php
$form = $this->beginWidget('CActiveForm',
	array(
	     'id'                     => 'report-form',
	     'enableAjaxValidation'   => true,
	     'enableClientValidation' => true,
	     'action'                 => Yii::app()->createUrl('/reports/default/create',
		     array(
		          'modelName' => get_class($model),
		          'modelId'   => $model->getId()
		     )),
	     'clientOptions'          => array(
		     'validateOnSubmit' => true,
		     'afterValidate'    => 'js:function(form,data,hasError){
		                           if(!hasError){
		                                   $.ajax({
		                                           type:"POST",
		                                           url:$(form).attr("action"),
		                                           data:form.serialize(),
		                                           dataType: "json",
		                                           success:function(data) {
		                                                $("#reportModal").modal("hide");
		                                           },
		                                   });
		                           }
		                           }'

	     ),

	));
?>

	<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h4><?php echo Yii::t('reportsModule.common',
			'Report for {model} {title}',
			array(
			     '{model}' => get_class($model),
			     '{title}' => $model->getTitle(),
			)); ?></h4>
</div>

	<div class="modal-body">
		<?php echo $form->textArea($report, 'text'); ?>
		<?php echo $form->error($report, 'text'); ?>
		<?php echo $form->hiddenField($report, 'modelName') ?>
		<?php echo $form->hiddenField($report, 'modelId') ?>
</div>

	<div class="modal-footer">
	<?php
		echo CHtml::submitButton(Yii::t('reportsModule.common', 'Report'),
			array('class' => 'btn btn-primary'));
		echo CHtml::button(Yii::t('reportsModule.common', 'Cancel'),
			array(
			     'class'        => 'btn btn-cancel',
			     'data-dismiss' => 'modal'
			));
		?>
</div>
<?php
$this->endWidget();
?>