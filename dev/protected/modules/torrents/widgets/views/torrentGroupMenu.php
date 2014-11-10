<?php

$this->widget('bootstrap.widgets.TbMenu',
	array(
	     'type'        => 'pills',
	     'encodeLabel' => false,
	     'htmlOptions' => array(
		     'class' => 'torrentGroupOperations nav-justified'
	     ),
	     'items'       => $items
	));