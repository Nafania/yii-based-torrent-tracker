<?php $this->widget('ext.bootstrap.widgets.TbGridView',
	array(
	     'id'           => 'fileList-grid-' . $model->getId(),
	     'dataProvider' => $dataProvider,
	     'template'     => '{items}{pager}',
	     'type'         => 'stripped',
	     'htmlOptions'  => array(
		     'class' => 'grid-view fileList-view',
	     ),
	     'cssFile'      => false,
	     'columns'      => array(
		     array(
			     'name'   => 'filename',
			     'header' => Yii::t('torrentsModule.common', 'Название файла'),
			     'type'   => 'raw',
			     'value'  => 'CHtml::encode($data["filename"])'
		     ),
		     array(
			     'name'   => 'size',
			     'header' => Yii::t('torrentsModule.common', 'Размер'),
			     'type'   => 'raw',
			     'value'  => 'SizeHelper::formatSize($data["size"])'
		     ),
	     ),
	));