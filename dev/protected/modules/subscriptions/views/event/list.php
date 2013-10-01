<?php
$eventItems = array();
foreach ( $events AS $event ) {
	$icon = $event->getIcon();
	$eventItems[] = array(
		'label'       => '<i class="icon-' . $icon . '"></i> ' . CHtml::encode($event->getTitle()),
		'url'         => $event->getUrl(),
		'linkOptions' => array(
			'data-toggle'         => 'tooltip',
			'data-original-title' => $event->getText(),
			'data-placement'      => 'right',
		)
	);
}

$this->widget('bootstrap.widgets.TbDropdown',
	array(
	     'encodeLabel' => false,
	     'items' => $eventItems,
	     'id' => 'eventsList',
	));