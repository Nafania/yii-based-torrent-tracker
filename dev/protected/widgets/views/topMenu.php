<?php
/**
 * @var $items              array
 * @var $form               TbActiveForm
 * @var $categories         Category[]
 * @var $selectedCategories array
 * @var $selectedTags       string
 * @var $notTags            string
 * @var $settingActive      boolean
 */
?>
<?php

if ( Yii::app()->getUser()->checkAccess('savedsearches.default.create') ) {
	$form = '
	<form class="form-search navbar-search pull-right" action="' . Yii::app()->createUrl('/torrents/default/index') . '">
	    <div class="input-prepend">
	        <a href="#searchSettings" class="add-on' . ($settingActive ? ' active' : '') . '" data-toggle="modal">
	            <i class="icon-cog' . ($settingActive ? ' icon-white' : '') . '" title="' . Yii::t('torrentsModule.common', 'Расширенный поиск') . '" data-toggle="tooltip" data-placement="bottom"></i></a>' .
        CHtml::textField('search',
			$searchVal,
			array(
			     'class'       => 'input-medium',
			     'placeholder' => Yii::t('common', 'Поиск')
			)) . '
			</div>
			<div class="input-append">
			<button type="submit" class="btn"><i class="icon-search"></i></button>
			</div>
		</form>';
}
else {
	$form = '<form class="form-search navbar-search pull-right" action="' . Yii::app()->createUrl('/torrents/default/index') . '">' . CHtml::textField('search',
			$searchVal,
			array(
			     'class'       => 'input-medium',
			     'placeholder' => Yii::t('common', 'Поиск')
			)) . '<div class="input-append"><button type="submit" class="btn"><i class="icon-search"></i></button></div></form>';
}


$this->widget('bootstrap.widgets.TbNavbar',
	array(
	     'type'     => null,
	     'fixed'    => false,
	     // null or 'inverse'
	     'brand'    => CHtml::image(Yii::app()->config->get('base.logoUrl'), Yii::app()->config->get('base.siteName')),
	     //'brandUrl' => '#',
	     'collapse' => true,
	     // requires bootstrap-responsive.css
	     'items'    => array(
		     $items,
		     $form

	     )
	));
if ( Yii::app()->getUser()->checkAccess('savedsearches.default.create') ) {

	$this->getController()->beginClip('afterContent');

	$this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'searchSettings'));
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
		array(
		     'id'                     => 'search-form',
		     'enableAjaxValidation'   => true,
		     'enableClientValidation' => true,
		     'action'                 => Yii::app()->createUrl('/torrents/default/index'),
		     'method'                 => 'get',
		)); ?>
	<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h4><?php echo Yii::t('torrentsModule.common', 'Расширенный поиск'); ?></h4>
</div>
	<div class="modal-body">
	<div class="row-fluid">
	<div class="control-group searchSettings clearfix">
		<div class="span5">
		<?php
		echo CHtml::label(Yii::t('common', 'Поисковая фраза'), 'search');
		echo CHtml::textField('search', $searchVal, array('class' => 'span11'));
		?>
		</div>

		<div class="span4">
		<?php
		echo CHtml::label(Yii::t('common', 'Сортировка'), 'sort');
		echo CHtml::dropDownList('sort',
			$sortVal,
			modules\torrents\models\TorrentGroup::getSortColums(),
			array(
			     'class' => 'input-medium',
			     'empty' => '',
			));
		?>
		</div>

		<div class="span3">
		<?php
		echo CHtml::label(Yii::t('common', 'Период'), 'period');
		echo CHtml::dropDownList('period',
			$periodVal,
			Savedsearch::getDatePeriods(),
			array(
			     'class' => 'input-medium',
			     'empty' => '',
			));
		?>
		</div>

	</div>
	<div class="control-group categoriesList clearfix">
	    <label><?php echo Yii::t('categoryModule.common', 'Категории') ?></label>
		<?php
		foreach ( $categories AS $category ) {
			$val = $category->getTitle();

			echo '<label class="checkbox span1">' . CHtml::checkBox('category[]',
					@in_array($val, $selectedCategories),
					array('value' => $val)) . ' ' . $val . '</label>';
		}
		?>
	</div>
		<?php
		echo CHtml::label(Yii::t('tagsModule.common', 'Теги'), 'tags');
		$this->widget('bootstrap.widgets.TbSelect2',
			array(
			     'asDropDownList' => false,
			     'name'           => 'tags',
			     'value'          => $selectedTags,
			     'htmlOptions'    => array(
				     'class' => 'span12 clearfix'
			     ),
			     'options'        => array(
				     'dropdownCssClass'   => 'input-xxlarge',
				     //'width' => '40.1709%',

				     'minimumInputLength' => 2,
				     'multiple'           => true,
				     'tokenSeparators'    => array(
					     ',',
				     ),
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

		<?php
		echo CHtml::label(Yii::t('tagsModule.common', 'Исключить теги'), 'notTags');
		$this->widget('bootstrap.widgets.TbSelect2',
			array(
			     'asDropDownList' => false,
			     'name'           => 'notTags',
			     'value'          => $notTags,
			     'htmlOptions'    => array(
				     'class' => 'span12 clearfix'
			     ),
			     'options'        => array(
				     'dropdownCssClass'   => 'input-xlarge',
				     //'width' => '40.1709%',

				     'minimumInputLength' => 2,
				     'multiple'           => true,
				     'tokenSeparators'    => array(
					     ',',
				     ),
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

		</div>
</div>


	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType'  => 'submit',
			     'type'        => 'primary',
			     'loadingText' => Yii::t('torrentsModule.common', 'Идет поиск...'),
			     'label'       => Yii::t('torrentsModule.common', 'Искать'),
			)); ?>
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType'  => 'ajaxLink',
			     'url'         => Yii::app()->createUrl('/savedsearches/default/create',
				     array('modelName' => 'modules_torrents_models_TorrentGroup')),
			     'type'        => 'link',
			     'label'       => Yii::t('savedsearchesModule.common', 'Запомнить настройки'),

			     'ajaxOptions' => array(
				     'type'     => 'post',
				     'dataType' => 'json',
				     'success'  => "js:function(data){
				                $('.top-right').notify({
				                     message: { html: data.message },
				                     type: 'success'
				                 }).show();}",
			     ),
			     'htmlOptions' => array(
				     'data-toggle'    => 'tooltip',
				     'title'          => Yii::t('savedsearchesModule.common',
					     'Ваши настройки поиска будут запомнены и в дальнейшем будут применяться к поиску.'),
				     'data-placement' => 'top',
			     ),
			)); ?>
</div>

	<?php $this->endWidget(); ?><!-- endform -->
	<?php $this->endWidget(); ?><!-- endmodal -->

	<?php $this->getController()->endClip('afterContent'); ?>

<?php } ?>