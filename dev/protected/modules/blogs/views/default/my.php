<?php
/* @var $this DefaultController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'id'              => 'blogsListView',
	     'dataProvider'    => $dataProvider,
	     'itemView'        => '_view',
	     'template'        => '{items} {pager}',
	));
?>
<?php
if ( Yii::app()->user->checkAccess('blogs.default.create') ) {
?>
<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType' => 'link',
		     'type'       => 'primary',
		     'label'      => Yii::t('blogsModule.common', 'Создать блог'),
		     'url' => Yii::app()->createUrl('/blogs/default/create'),

		));
	?>
</div>
<?php } ?>
