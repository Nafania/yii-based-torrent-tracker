<?php
/* @var $this DefaultController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'id'              => 'groupsListView',
	     'dataProvider'    => $dataProvider,
	     'itemView'        => '_viewMy',
	     'template'        => '{items} {pager}',
	));
?>
<?php
if ( Yii::app()->user->checkAccess('groups.default.create') ) {
?>
<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
		array(
		     'buttonType' => 'link',
		     'type'       => 'primary',
		     'label'      => Yii::t('groupsModule.common', 'Создать группу'),
		     'url' => Yii::app()->createUrl('/groups/default/create'),

		));
	?>
</div>
<?php } ?>
