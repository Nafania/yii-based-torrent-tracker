<?php
/**
 * @var TbActiveForm $form
 * @var Category     $category
 * @var TorrentGroup $model
 */
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'torrent-form',
	     'enableClientValidation' => true,
	     'method'                 => 'get',
	     'action'                 => Yii::app()->createUrl('/torrents/default/create')
	)); ?>

<?php echo $form->errorSummary(array(
                                    $category,
                                    $model
                               )); ?>

<?php echo $form->dropDownListRow($category,
	'id',
	CHtml::listData(Category::model()->findAll(), 'id', 'name'),
	array(
	     'empty' => '-',
	     'class' => 'span4'
	)); ?>

<div>
<?php
echo $form->labelEx($model,
	'title',
	array(
	     'data-toggle'         => 'tooltip',
	     'data-placement'      => 'right',
	     'title' => Yii::t('torrentsModule.common',
		     'Впишите сюда общее название, например, название фильма, игры, программы, автора музыкального альбома или книги и выберете его в выпадающем списке, если оно появится.'),
	     'class'               => 'attributeDescription',
	));
?>
	</div>

<?php
$this->widget('bootstrap.widgets.TbSelect2',
	array(
	     'asDropDownList' => false,
	     'model'          => $model,
	     'attribute'      => 'title',
	     'htmlOptions'    => array(
		     'class' => 'span4'
	     ),
	     'options'        => array(
		     //'containerCssClass' => 'span5',
		     'minimumInputLength' => 2,
		     'multiple'           => false,
		     //'tags'               => false,
		     'ajax'               => 'js:{
				url: ' . CJavaScript::encode(Yii::app()->createUrl('/torrents/default/suggest')) . ',
                dataType: "json",
                cache: true,
                quietMillis: 100,
                data: function ( term ) {
				return {
					term: term, category: $("#Category_id").val()
                    };
                },
                results: function ( data ) {
					return {
						results: data.data.titles};
                }}',
		     'createSearchChoice' => 'js:function(term, data) {
		              if ( $(data).filter( function() {
		                return this.text.localeCompare(term)===0;
		              }).length===0) {
		                return {id:0, text:term};
		              }}',
	     ),
	     'events'         => array(
		     'change' => 'js:function(e){$("#gId").val(e.val);}'
	     )
	));
echo $form->error($model, 'title');
echo CHtml::hiddenField('gId', Yii::app()->getRequest()->getParam('gId', 0));
?>

<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => Yii::t('torrentsModule.common', 'Дальше'),
			)); ?>
	</div>

<?php $this->endWidget(); ?>
