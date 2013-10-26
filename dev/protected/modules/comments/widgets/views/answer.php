<?php
/* @var $this AnswerWidget */
/* @var $comment Comment */
/* @var $form TbActiveForm */
?>
<?php if ( !Yii::app()->getUser()->checkAccess('comments.default.create') ) {
	return;
}
?>
<div class="answerBlock">
<?php
$form = $this->beginWidget('ext.bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'comment-form' . uniqid(),
	     'enableAjaxValidation'   => true,
	     'enableClientValidation' => true,
	     'action'                 => Yii::app()->createUrl('/comments/default/create'),
	     'htmlOptions'            => array(
		     'class' => 'answerForm'
	     ),
	     'clientOptions'          => array(
		     'validateOnSubmit' => true,
		     'afterValidate'    => 'js:function(form,data,hasError){
		        var button = form.find("input[type=submit]");
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
                                                $(data.data.view).appendTo("#comment-" + data.data.parentId + " > div.media-body");
                                            }
                                            else {
                                                if ( $(".commentContainer").length ) {
                                                    $(data.data.view).insertAfter(".commentsBlock > .commentContainer:last-child");
                                                }
                                                else {
                                                    $(data.data.view).appendTo(".commentsBlock");
                                                }
                                            }
                                            $("html, body").animate({scrollTop: $("#comment-" + data.data.id).position().top }, 100);
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

	));
?>
	<?php //echo $form->labelEx($comment, 'torrentId'); ?>
	<?php if ( !$parentId && $torrents ) {
		echo $form->dropDownListRow($comment,
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
		'Отправить комментарий') : Yii::t('commentsModule.common', 'Обновить комментарий'),
	array('class'            => 'btn btn-primary',
	     'data-loading-text' => Yii::t('commentsModule.common', 'Идет отправка...'),
	));
?>
</div>
	<?php
	$this->endWidget();
	?>
</div>