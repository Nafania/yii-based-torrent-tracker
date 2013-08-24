<?php
/* @var $this PostController */
/* @var $blogPost BlogPost */
/* @var $form TbActiveForm */
?>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                   => 'blogPost-form',
	     'enableAjaxValidation' => true,
	)); ?>

<?php echo $form->textFieldRow($blogPost, 'title', array('class' => 'span5')); ?>

<?php echo $form->labelEx($blogPost, 'text'); ?>
<?php $this->widget('application.extensions.imperavi-redactor-widget.ImperaviRedactorWidget',
	array(
	     // you can either use it for model attribute
	     'model'       => $blogPost,
	     'attribute'   => 'text',

	     //TODO load actual language
	     'htmlOptions' => array(),
	     'options'     => array(
		     'lang'          => 'ru',
		     'minHeight'     => 200,
		     'imageUpload'   => Yii::app()->createUrl('files/default/upload'),
		     'uploadFields'  => array(
			     Yii::app()->getRequest()->csrfTokenName => Yii::app()->getRequest()->getCsrfToken(),
			     'modelName'                             => get_class($blogPost)
		     )
	     ),
	));?>
<?php echo $form->error($blogPost, 'text'); ?>

<?php

echo CHtml::label('Tags', 'tags');
$this->widget('bootstrap.widgets.TbSelect2',
	array(
	     'asDropDownList' => false,
	     'name'           => 'tags',
	     'value'          => $blogPost->tags->toString(true),
	     'options'        => array(
		     //'containerCssClass' => 'span5',
		     'width'              => '40.1709%',

		     'minimumInputLength' => 2,
		     'multiple'           => false,
		     'tokenSeparators'    => array(
			     ',',
			     ' '
		     ),
		     'createSearchChoice' => 'js:function(term, data) {
			       if ($(data).filter(function() {
			         return this.text.localeCompare(term) === 0;
			       }).length === 0) {
			         return {
			           id: term,
			           text: term
			         };
			       }
			     }',
		     'tags'               => true,
		     'initSelection'      => 'js:function (element, callback) {
			         var data = [];
			         $(element.val().split(",")).each(function (key, val) {
			             data.push({id: val, text: val});
			         });
			         callback(data);
			     }',
		     'ajax'               => 'js:{
					url: ' . CJavaScript::encode(Yii::app()->createUrl('/blogs/post/tagsSuggest',
			     array('blogId' => $blog->getId()))) . ',
	                dataType: "json",
	                cache: true,
	                quietMillis: 100,
	                data: function ( term ) {
					return {
						q: term,
	                    };
	                },
	                results: function ( data ) {
						return {
							results: data.data.tags};
	                }}',
	     )
	));
?>

	<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => ($blogPost->isNewRecord ? 'Create' : 'Save'),
			)); ?>
	</div>

<?php $this->endWidget(); ?>