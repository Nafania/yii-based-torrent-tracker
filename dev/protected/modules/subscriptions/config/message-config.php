<?php
return array(
	'sourcePath'  => Yii::getPathOfAlias('application.modules.subscriptions'),
	'messagePath' => Yii::getPathOfAlias('application.modules.subscriptions.messages'),
	'languages'   => array('ru'),
	'fileTypes'   => array(
		'php',
		'html'
	),
	'exclude'     => array(),
	'translator'  => 'Yii::t',
);