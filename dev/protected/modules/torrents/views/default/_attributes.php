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
	$description = ( $attribute->description ? ' <span class="attributeDescription"  data-toggle="tooltip" data-placement="top" data-original-title="' . $attribute->description . '">?</span>' : '' );
	//}

	$hasErrors = $attribute->hasErrors();

	echo CHtml::openTag('div', array('class' => 'attribute'));
	echo CHtml::label($attribute->title . $description . $required,
		'Attribute_' . $attribute->id,
		array(
		     'class' => ($attribute->required ? 'required' : '') . ($hasErrors ? ' error' : '')
		));
	echo '<div class="rowInput' . ($attribute->separate ? ' separate' : '') . '">' . $attribute->getInputField($value,
		$hasErrors, array('class' => 'span5')) . '</div>';
	if ( $hasErrors ) {
		echo '<span class="help-block error">';
		foreach ( $attribute->getErrors() AS $key => $val ) {
			echo implode('<br />', $val);
		}
		echo '</span>';
	}

	echo CHtml::closeTag('div');
}
