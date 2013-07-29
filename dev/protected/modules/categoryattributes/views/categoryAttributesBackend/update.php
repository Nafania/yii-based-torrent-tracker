<?php
/**
 * @var CActiveForm       $form
 * @var Attribute $model
 * @var CategoryAttrChars $chars
 * @var $counter
 */

Yii::app()->getClientScript()->registerScript('CategoryAttribute_type',
	'var counter = ' . $counter . ';
	$("#CategoryAttribute_type").change(function(){
	if ( $(this).val() == ' . Attribute::TYPE_CHECKBOX . ' || $(this).val() == ' . Attribute::TYPE_DROPDOWN . ' || $(this).val() == ' . Attribute::TYPE_RADIO . ' ) {
		$(".characteristics").show();
	}
	else {
		$(".characteristics").hide();
	}

});');

Yii::app()->getClientScript()->registerScript('deleteHandler', "$(document).on('click', 'a.delete',function() {
	if ( confirm('" . YiiadminModule::t('Вы уверены, что хотите удалить данный элемент?') . "') ) {
		if ( $('.characteristics').children('fieldset').length > 1 ) {
			$(this).parents('fieldset').remove();
		}
		else {
			$(this).parents('fieldset').find('input,textarea,select').val('');
		}
	}
	return false;});"
);

Yii::app()->getClientScript()->registerScript('addHandler', "$(document).on('click', 'a.add-handler', function() {
	++counter;
	var fieldset = $(this).parents('.characteristics').find('fieldset').last();
	var clone = fieldset.clone();
	var html;
	var oldCounter = counter - 1;
	html = clone.html();
	html = html.replace(new RegExp('_' + oldCounter + '_','g'), '_' + counter + '_');
	html = html.replace(new RegExp('\\\[' + oldCounter + '\\\]', 'g'), '[' + counter + ']');
	clone.html(html);
	clone.find('input,textarea,select').val('');
	clone.find('input,textarea,select').removeClass('error');
	clone.find('.errorMessage').hide();
	$(clone).insertAfter(fieldset);
	return false;});"
);

echo '<h1>' . Yii::t('CategoryAttributesModule', 'Редактирование аттрибута') . '</h1>';
$this->renderPartial('_form', array(
                                   'model' => $model,
                                   'chars' => $chars,
                                   'counter' => $counter
                              ));