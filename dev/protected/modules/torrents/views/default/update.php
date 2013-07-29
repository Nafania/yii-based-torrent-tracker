<?php
$this->breadcrumbs=array(
	'Torrents'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Torrent','url'=>array('index')),
	array('label'=>'Create Torrent','url'=>array('create')),
	array('label'=>'View Torrent','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Torrent','url'=>array('admin')),
);
?>

<h1>Update Torrent <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>