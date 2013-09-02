<?php
/**
 * @var TbActiveForm $form
 * @var TorrentGroup $torrentGroup
 * @var Torrent      $torrent
 * @var Attribute[]  $attributes
 */
?>
<?php

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'torrent-form',
	     'enableClientValidation' => true,
	     'htmlOptions'            => array(
		     'enctype' => 'multipart/form-data'
	     )
	)); ?>

<?php
echo $form->fileFieldRow($torrent, 'info_hash', array('class' => 'span5'));

$this->renderPartial('_attributes',
	array(
	     'model'      => $torrent,
	     'attributes' => $attributes
	));

echo CHtml::label('Tags', 'tags');
$this->widget('bootstrap.widgets.TbSelect2',
	array(
	     'asDropDownList' => false,
	     'name'           => 'tags',
	     'value'          => $torrent->tags->toString(true),
	     'options'        => array(
		     //'containerCssClass' => 'span5',
		     'width' => '40.1709%',

		     'minimumInputLength' => 2,
		     'multiple'           => true,
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
		         $(element.val().split(",")).each(function () {
		             data.push({id: this, text: this});
		         });
		         callback(data);
		     }',
		     'ajax'               => 'js:{
				url: ' . CJavaScript::encode(Yii::app()->createUrl('/torrents/default/tagsSuggest')) . ',
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
			     'label'      => 'Upload',
			)); ?>
	</div>

<?php $this->endWidget(); ?>
