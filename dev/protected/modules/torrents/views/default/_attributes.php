<?php
/**
 * @var $attributes Attribute[]
 */


foreach ( $attributes AS $attribute ) {
	if ( isset($_POST['Attribute'][$attribute->id]) ) {
		$value = $_POST['Attribute'][$attribute->id];
	}
	/*else {
		$value = '';
	}*/
	else {
		$value = $model->getEavAttribute($attribute->id);
	}
	$required = ($attribute->required ? ' <span class="required">*</span>' : '');
	$measure = ($attribute->measure ? ' <span class="measure">(' . $attribute->measure . ')</span>' : '');
	//}

	$hasErrors = $attribute->hasErrors();

	echo CHtml::openTag('div', array('class' => 'attribute'));
	echo CHtml::label($attribute->title . $measure . $required,
		'Attribute_' . $attribute->id,
		array(
		     'class' => ($attribute->required ? 'required' : '') . ($hasErrors ? ' error' : '')
		));
	echo '<div class="rowInput' . ($attribute->separate ? ' separate' : '') . '">' . $attribute->getInputField($value,
		$hasErrors) . '</div>';
	if ( $hasErrors ) {
		echo '<span class="help-block error">';
		foreach ( $attribute->getErrors() AS $key => $val ) {
			echo implode('<br />', $val);
		}
		echo '</span>';
	}

	echo CHtml::closeTag('div');
}
