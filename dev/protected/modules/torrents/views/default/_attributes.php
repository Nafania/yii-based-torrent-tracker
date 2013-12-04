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

	$hasErrors = $attribute->hasErrors();

	$htmlOptions = array(
		'class' => ($attribute->required ? 'required' : '') . ($hasErrors ? ' error' : '') . ( $attribute->description ? ' attributeDescription' : '' )
	);

	if ( $description = $attribute->description ) {
		$htmlOptions = CMap::mergeArray($htmlOptions,
			array(
			     'data-toggle'         => 'tooltip',
			     'data-placement'      => 'right',
			     'title' => $description,
			));
	}


	echo CHtml::openTag('div', array('class' => 'attribute'));
	echo CHtml::label($attribute->title . $required,
		'Attribute_' . $attribute->id,
		$htmlOptions);
	echo '<div class="rowInput' . ($attribute->separate ? ' separate' : '') . '">' . $attribute->getInputField($value,
			$hasErrors,
			array('class' => 'span5')) . '</div>';
	if ( $hasErrors ) {
		echo '<span class="help-block error">';
		foreach ( $attribute->getErrors() AS $key => $val ) {
			echo implode('<br />', $val);
		}
		echo '</span>';
	}

	echo CHtml::closeTag('div');
}
