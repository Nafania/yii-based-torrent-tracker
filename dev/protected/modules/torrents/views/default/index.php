<?php
if ( !empty($pageSize) ) {
	$this->renderPartial('application.modules.torrents.views.default.indexTop', array('pageSize' => $pageSize));
}
?>
<?php $this->widget('bootstrap.widgets.TbListView',
	array(
		'dataProvider'       => $dataProvider,
		'id'                 => 'torrents-list',
		'itemView'           => 'application.modules.torrents.views.default._view',
		'template'           => "{items}\n{pager}",
		'enableHistory'      => true,
		'sortableAttributes' => array(
			'mtime',
			'commentsCount',
			'rating',
		),
	));
