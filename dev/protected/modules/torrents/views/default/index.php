<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'dataProvider'       => $dataProvider,
	     'itemView'           => '_view',
	     'template'           => "{sorter}\n{items}\n{pager}",
	     'enableHistory'      => true,
	     'sortableAttributes' => array(
		     'mtime',
	     ),
	));
