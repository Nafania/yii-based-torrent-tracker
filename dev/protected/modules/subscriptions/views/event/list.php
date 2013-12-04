<?php
$eventItems = array();
foreach ( $events AS $event ) {
	$icon = $event->getIcon();
	$eventItems[] = array(
		'label'       => '<i class="icon-' . $icon . '"></i> ' . CHtml::encode($event->getTitle()),
		'url'         => $event->getUrl(),
		'linkOptions' => array(
			'data-toggle'    => 'tooltip',
			'title'          => $event->getText(),
			'data-placement' => 'right',
			'data-id'        => $event->getId(),
			'data-action'    => 'event',
		),

	);
}

$this->widget('bootstrap.widgets.TbDropdown',
	array(
	     'encodeLabel' => false,
	     'items'       => $eventItems,
	     'id'          => 'eventsList',
	));