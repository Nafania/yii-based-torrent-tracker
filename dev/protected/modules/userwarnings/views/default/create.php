<?php
/**
 * @var $form    CActiveForm
 * @var $warning \modules\userwarnings\models\UserWarning
 */
?>

<?php if ( !Yii::app()->getUser()->checkAccess('userwarnings.default.create') ) {
	return;
}
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
		'id'                     => 'warning-form',
		'enableAjaxValidation'   => true,
		'enableClientValidation' => true,
		'action'                 => Yii::app()->createUrl('/userwarnings/default/create'),
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
		                                    $("#warningModal").modal("hide");
		                                    $(".top-right").notify({
		                                        message: { html: data.message },
		                                        fadeOut: {
		                                            enabled: true,
		                                            delay: 9000
		                                        },
		                                        type: "success"
		                                    }).show();
		                                    location.reload();
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
</div>

	<div class="modal-body row-fluid">
		<?php echo $form->textAreaRow($warning, 'text', array('class' => 'span11')); ?>
		<?php echo $form->error($warning, 'text'); ?>
		<?php echo $form->hiddenField($warning, 'uId', array('name' => 'uId')); ?>
		<?php echo $form->error($warning, 'uId'); ?>
	</div>

	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
				'buttonType'  => 'submit',
				'type'        => 'primary',
				'label'       => Yii::t('userwarningsModule.common', 'Выдать предупреждение'),
				'loadingText' => Yii::t('userwarningsModule.common', 'Идет отправка...'),
			)); ?>

		<?php
		echo CHtml::button(Yii::t('userwarningsModule.common', 'Отмена'),
			array(
				'class'        => 'btn btn-cancel',
				'data-dismiss' => 'modal'
			));
		?>
</div>
<?php
$this->endWidget();
?>