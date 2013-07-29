<?php
return array(
	'sourcePath'  => Yii::getPathOfAlias('application'),
	'messagePath' => Yii::getPathOfAlias('application.messages'),
	'languages'   => array('ru'),
	'fileTypes'   => array(
		'php',
		'html'
	),
	'exclude'     => array(),
	'translator'  => 'Yii::t',
);