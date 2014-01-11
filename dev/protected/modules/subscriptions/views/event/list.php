<?php
/**
 * @var $events Event[]
 */
?>
<?php
$eventItems = array();

foreach ( $events AS $event ) {
	$icon = $event->getIcon();
	$eventItems[] = array(
		'label'       => '<i class="icon-' . $icon . '"></i> ' . CHtml::encode($event->getTitle()),
		'url'         => $event->getUrl(),
		'linkOptions' => array(
			'data-toggle'    => 'tooltip',
			'title'          => $event->getText() . ( $event->count > 1 ? PHP_EOL . Yii::t('subscriptionsModule.common', 'Это событие произошло {n} раз.|Это событие произошло {n} раза.|Это событие произошло {n} раз.|Это событие произошло {n} раза.', $event->count) : '' ),
			'data-placement' => 'right',
			'data-id'        => $event->getId(),
			'data-action'    => 'event',
		),

	);
}

if ( !$eventItems ) {
	$eventItems[] = array(
		'label' => Yii::t('subscriptionsModule.common', 'У вас нет новых уведомлений'),
		'url'   => '#',
	);
}

$this->widget('bootstrap.widgets.TbDropdown',
	array(
		'encodeLabel' => false,
		'items'       => $eventItems,
		'id'          => 'eventsList',
	));