<a id="listTop"></a>
<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'dataProvider'       => $dataProvider,
	     'itemView'           => '_view',
	     'template'           => "{items}\n{pager}",
	     'enableHistory'      => true,
	     'sortableAttributes' => array(
		     'mtime',
		     'commentsCount',
		     'rating',
	     ),
	));
