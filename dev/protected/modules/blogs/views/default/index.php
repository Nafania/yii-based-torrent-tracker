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
