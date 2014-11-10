<?php
/**
 * @var $form  CActiveForm
 * @var $model ReportContent
 */
?>

<?php if ( !Yii::app()->getUser()->checkAccess('reports.default.create') ) {
	return;
}
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'report-form',
	     'enableAjaxValidation'   => true,
	     'enableClientValidation' => true,
	     'action'                 => Yii::app()->createUrl('/reports/default/create',
		     array(
		          'modelName' => $model->resolveClassName(),
		          'modelId'   => $model->getId()
		     )),
	     'clientOptions'          => array(
		     'validateOnSubmit' => true,
		     'afterValidate'    => 'js:function(form,data,hasError){
		     var button = form.find("button[type=submit]");
		                           if(!hasError){
		                                $.ajax({
		                                type:"POST",
		                                url:$(form).attr("action"),
		                                data:form.serialize(),
		                                dataType: "json",
		                                success:function(data) {
		                                    $("#reportModal").modal("hide");
		                                    $(".top-right").notify({
		                                        message: { html: data.message },
		                                        fadeOut: {
		                                            enabled: true,
		                                            delay: 9000
		                                        },
		                                        type: "success"
		                                    }).show();
		                                },
		                                complete: function(data){
		                                    button.button("reset");
		                                }
		                                   });
		                           }
		                           else {
		                            button.button("reset");
		                           }
		                           }'

	     ),

	));
?>

	<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h4><?php echo Yii::t('reportsModule.common',
			'Жалоба на "{title}"',
			array(
			     '{title}' => $model->getTitle(),
			)); ?></h4>
</div>

	<div class="modal-body">
		<?php echo $form->textArea($report, 'text'); ?>
		<?php echo $form->error($report, 'text'); ?>
		<?php echo $form->hiddenField($report, 'rId'); ?>
		<?php echo $form->error($report, 'rId'); ?>
	</div>

	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType'  => 'submit',
			     'type'        => 'primary',
			     'label'       => Yii::t('reportsModule.common', 'Пожаловаться'),
			     'loadingText' => Yii::t('reportsModule.common', 'Идет отправка...'),
			)); ?>

		<?php
		echo CHtml::button(Yii::t('reportsModule.common', 'Отмена'),
			array(
			     'class'        => 'btn btn-cancel',
			     'data-dismiss' => 'modal'
			));
		?>
</div>
<?php
$this->endWidget();
?>