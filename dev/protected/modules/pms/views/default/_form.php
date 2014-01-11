<?php
/**
 * @var PrivateMessage $model
 * @var TbActiveForm   $form
 */
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
		'id'                     => 'pms-form',
		'enableClientValidation' => true,
	)); ?>

<?php echo $form->labelEx($model, 'receiverUid'); ?>
<?php
$this->widget('bootstrap.widgets.TbSelect2',
	array(
		'asDropDownList' => false,
		'model'          => $model,
		'attribute'      => 'receiverUid',
		'htmlOptions'    => array(
			'class'       => 'span5',
			'multiple'    => 'multiple',
			'placeholder' => Yii::t('pmsModule.common',
					'Имена получателей'),
		),
		'options'        => array(
			//'containerCssClass' => 'span5',

			'minimumInputLength' => 2,
			'multiple'           => true,
			'tags'               => true,
			'tokenSeparators'    => array(
				',',
			),
			'initSelection'      => 'js:function (element, callback) {
					         var data = [];
					         var usersData = ' . CJavaScript::encode($usersData) . ';
					         $.each(usersData, function (key,user) {
					             data.push({id:user.id, text: user.text});
					         });
					         callback(data);
					     }',
			'ajax'               => 'js:{
					url: ' . CJavaScript::encode(Yii::app()->createUrl('/user/default/suggest')) . ',
	                dataType: "json",
	                cache: true,
	                quietMillis: 100,
	                data: function ( term ) {
					return {
						term: term
	                    };
	                },
	                results: function ( data ) {
						return {
							results: data.data.users};
	                }}',
		)
	));
?>
<?php echo $form->error($model, 'receiverUid'); ?>

<?php echo $form->textFieldRow($model, 'subject', array('class' => 'span5')); ?>

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
</div>

	<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
			'buttonType' => 'submit',
			'type'       => 'primary',
			'label'      => Yii::t('common', 'Отправить'),
		)); ?>
	</div>

<?php $this->endWidget(); ?>