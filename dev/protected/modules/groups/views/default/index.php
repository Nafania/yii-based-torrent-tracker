<?php
/* @var $this DefaultController */
/* @var $dataProvider CActiveDataProvider */
/* @var $model Group */
?>

<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'id'              => 'groupsListView',
	     'dataProvider'    => $dataProvider,
	     'itemView'        => '_view',
	     'template'        => "{sorter}\n{items} {pager}",
	     'sortableAttributes' => array(
		     'ctime',
		     'rating',
	     ),
	));
?>
