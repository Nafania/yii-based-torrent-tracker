<?php
/* @var $this AnswerWidget */
/* @var $comment Comment */
/* @var $form CActiveForm */
?>
<?php if ( !Yii::app()->getUser()->checkAccess('comments.default.create') ) {
	return;
}
?>
<div class="answerBlock">
<?php
	$form = $this->beginWidget('CActiveForm',
		array(
		     'id'                     => 'comment-form' . uniqid(),
		     'enableAjaxValidation'   => true,
		     'enableClientValidation' => true,
		     'action'                 => Yii::app()->createUrl('/comments/default/create'),
		     'clientOptions'          => array(
			     'validateOnSubmit' => true,
			     'afterValidate'    => 'js:function(form,data,hasError){
                       if(!hasError){
                               $.ajax({
                                       type:"POST",
                                       url:"' . Yii::app()->createUrl('/comments/default/create') . '",
                                       data:form.serialize(),
                                       dataType: "json",
                                       success:function(data) {
                                            $("#Comment_text").redactor("set", "");
                                            $(form).prevAll(".commentReply").data("activated", 0);
                                            if ( data.data.parentId ) {
                                                $(form).remove();
                                                $(data.data.view).appendTo("#comment-" + data.data.parentId + " > div.comment");
                                            }
                                            else {
                                                if ( $(".commentContainer").length ) {
                                                    $(data.data.view).insertAfter(".commentsBlock > .commentContainer:last-child");
                                                }
                                                else {
                                                    $(data.data.view).appendTo(".commentsBlock");
                                                }
                                            }
                                       },

                                       });
                               }
                       }'
		     ),

		));
	?>
	<?php //echo $form->labelEx($comment, 'torrentId'); ?>
	<?php if ( !$parentId && $torrents ) {
		echo $form->dropDownList($comment,
			'torrentId',
			CHtml::listData($torrents,
				'id',
				function ( $data ) {
					return $data->getSeparateAttribute();
				}),
			array('empty' => ''));
	}
	?>

	<?php //echo $form->labelEx($comment, 'text'); ?>
	<?php $this->widget('application.extensions.imperavi-redactor-widget.ImperaviRedactorWidget',
		array(
		     // you can either use it for model attribute
		     'model'     => $comment,
		     'attribute' => 'text',

		     //TODO load actual language
		     'options'   => array(
			     'buttons'       => array(
				     'bold',
				     'italic',
				     'underline',
				     '|',
				     'link',
				     '|',
				     'image',
				     '|',
				     'quote'
			     ),
			     'buttonsCustom' => array(
				     'quote' => array(
					     'title'    => 'Quote',
					     'callback' => 'js:function(buttonName, buttonDOM, buttonObject) {
					        this.buttonActive(buttonName);
					        var text = "";
					        if (window.getSelection) {
					            text = window.getSelection().toString();
					        }
					        else {
					            if (document.selection.createRange) {
					                text = document.selection.createRange().text;
					            }
					        }
					        this.insertHtml(text);}',
				     ),
			     ),
			     'lang'          => 'ru',
			     /*'imageUpload'  => Yii::app()->createUrl('files/default/upload'),
			 'uploadFields' => array(
				 Yii::app()->getRequest()->csrfTokenName => Yii::app()->getRequest()->getCsrfToken(),
				 'modelName'                             => get_class($comment)
			 )*/
		     ),
		));?>
	<?php echo $form->error($comment, 'text'); ?>
	<?php echo $form->hiddenField($comment, 'modelName', array('value' => $modelName)); ?>
	<?php echo $form->hiddenField($comment, 'modelId', array('value' => $modelId)); ?>
	<?php echo $form->hiddenField($comment, 'parentId', array('value' => $parentId)); ?>

	<div class="form-actions">
<?php
		echo CHtml::submitButton($comment->isNewRecord ? Yii::t('commentsModule.common',
				'Send comment') : Yii::t('commentsModule.common', 'Update comment'),
			array('class' => 'btn btn-primary'));
		?>
</div>
	<?php
	$this->endWidget();
	?>
</div>