<?php
/**
 * @var TbActiveForm $from
 */
?>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'pms-form' . uniqid(),
	     'enableClientValidation' => false,
	     'enableAjaxValidation'   => true,
	     'action'                 => Yii::app()->createUrl('/pms/default/create'),
	     'htmlOptions' => array(
		     'class' => 'answerForm'
	     ),
	     'clientOptions'          => array(
		     'validateOnSubmit' => true,
		     'beforeValidate'   => 'js:function(form){
		     var parentId = form.prevAll(".well").data("id");
		     if ( !parentId ) {
		        parentId = form.prevAll(".messagesTree").find(".well:last").data("id");
		     }
		     form.find("#PrivateMessage_parentId").val(parentId);
		     return true;}',
		     'afterValidate'    => 'js:function(form,data,hasError){
		        var button = form.find("button[type=submit]");
                       if(!hasError){
                               $.ajax({
                                       type:"POST",
                                       url:"' . Yii::app()->createUrl('/pms/default/create') . '",
                                       data:form.serialize(),
                                       dataType: "json",
                                       success:function(data) {
                                            var parentId = form.prevAll(".well").data("id");
                                            if ( !parentId ) {
		                                        parentId = form.prevAll(".messagesTree").find(".well:last").data("id");
		                                    }
                                            var appendToElem = $("#message-" + parentId).parent(".pmContainer");
                                            form.find("#PrivateMessage_message").redactor("set", "");
                                            $(data.data.view).appendTo(appendToElem);
                                            if ( form.nextAll(".pmContainer").length ) {
                                                form.fadeOut("fast", function () {
                                                    $(this).remove();
                                                });
                                            }
                                            $(".top-right").notify({
                                                message: { html: data.message },
                                                fadeOut: {
                                                    enabled: true,
                                                    delay: 3000
                                                },
                                                type: "success"
                                            }).show();
                                            $("html, body").animate({scrollTop: $("#message-" + data.data.id).position().top }, 100);
                                       },
                                       complete: function() {
                                        button.button("reset");
                                       }
                                       });
                               }
                               else {
                                button.button("reset");
                               }

                       }'
	     ),

	)); ?>

	<div class="span5">
<?php echo $form->labelEx($model, 'message'); ?>
<?php $this->widget('application.extensions.imperavi-redactor-widget.ImperaviRedactorWidget',
	array(
	     // you can either use it for model attribute
	     'model'       => $model,
	     'attribute'   => 'message',

	     //TODO load actual language
	     'options'     => array(
		     'buttons' => array(
			     'bold',
			     'italic',
			     'underline',
			     '|',
			     'link',
			     '|',
			     'image',
		     ),
		     'lang'    => 'ru',
	     ),
	     'htmlOptions' => array( //'class' => 'span5'
	     )
	));?>
<?php echo $form->error($model, 'message'); ?>
<?php echo $form->hiddenField($model, 'parentId'); ?>
<?php echo $form->hiddenField($model, 'receiverUid'); ?>
<?php echo $form->hiddenField($model, 'subject'); ?>
<?php echo $form->error($model, 'parentId'); ?>
<?php echo $form->error($model, 'receiverUid'); ?>
<?php echo $form->error($model, 'subject'); ?>
</div>

	<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType'  => 'submit',
		     'type'        => 'primary',
		     'label'       => Yii::t('common', 'Отправить'),
		     'loadingText' => Yii::t('pmsModule.common', 'Идет отправка...'),
		)); ?>
	</div>

<?php $this->endWidget(); ?>