<a id="listTop"></a>
<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'dataProvider'       => $dataProvider,
	     'itemView'           => '_view',
	     'template'           => "{sorter}\n{items}\n{pager}",
	     'enableHistory'      => true,
	     'afterAjaxUpdate'    => 'function(){$("html, body").animate({scrollTop: $("#listTop").position().top }, 100);}',
	     'sortableAttributes' => array(
		     'mtime',
	     ),
	));
