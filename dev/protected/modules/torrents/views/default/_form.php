<?php
/**
 * @var TbActiveForm $form
 */
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                   => 'torrent-form',
	     'enableClientValidation' => true,
	     'method'               => 'get',
	     'action' => Yii::app()->createUrl('/torrents/default/create')
	)); ?>

<?php echo $form->errorSummary(array($category, $model)); ?>

<?php echo $form->dropDownListRow($category,
	'id',
	CHtml::listData(Category::model()->findAll(), 'id', 'name'),
	array('empty' => '-')); ?>

<?php
echo $form->labelEx($model, 'title');
$this->widget('zii.widgets.jui.CJuiAutoComplete',
	array(
	     'model' => $model,
	     'attribute' => 'title',
	     //'name'        => 'suggest',
	     //'value' => Yii::app()->getRequest()->getParam('suggest', ''),
	     'source'      => 'js:function( request, response ) {
	        $.ajax({
	            url: ' . CJavaScript::encode(Yii::app()->createUrl('/torrents/default/suggest')) . ',
	            dataType: "json",
	            data: {
	                term: request.term,
	                category: $("#Category_id").val()
	            },
	            success: function( data ) {
	                response( $.map( data.data, function( item ) {
	                    return {
	                        label: item.title,
	                        value: item.id
	                    }
	                }));
	            }
	        });
	     }',
	     // additional javascript options for the autocomplete plugin
	     'options'     => array(
		     'minLength' => '2',
		     'select'    => 'js:function(event,ui){$(this).val(ui.item.label);$("#gId").val(ui.item.value);return false;}',
		     'focus'     => 'js:function(event,ui){$(this).val(ui.item.label);$("#gId").val(ui.item.value);return false;}'
	     ),
	     'htmlOptions' => array(
		     'style' => 'height:20px;',
	     ),
	));
echo CHtml::hiddenField('gId', Yii::app()->getRequest()->getParam('gId', 0));
?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => 'Next',
			)); ?>
	</div>

<?php $this->endWidget(); ?>
