<?php $this->widget('zii.widgets.grid.CGridView',
	array(
	     'id' => 'fileList-grid',
	     'dataProvider' => $dataProvider,
	     'template'     => '{items}{pager}',
	     'cssFile' => false,
	     'columns'      => array(
		     array(
			     'name' => 'filename',
			     'header' => Yii::t('torrentsModule.common', 'Filename'),
			     'type' => 'raw',
			     'value' => 'CHtml::encode($data["filename"])'
		     ),
		     array(
			     'name'  => 'size',
			     'header' => Yii::t('torrentsModule.common', 'Size'),
			     'type'  => 'raw',
			     'value' => 'SizeHelper::formatSize($data["size"])'
		     ),
	     ),
	));