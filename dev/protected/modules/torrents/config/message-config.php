<?php
return array(
	'sourcePath'  => Yii::getPathOfAlias('application.modules.torrents'),
	'messagePath' => Yii::getPathOfAlias('application.modules.torrents.messages'),
	'languages'   => array('ru'),
	'fileTypes'   => array(
		'php',
		'html'
	),
	'exclude'     => array(),
	'translator'  => 'Yii::t',
);