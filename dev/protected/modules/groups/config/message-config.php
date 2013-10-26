<?php
return array(
	'sourcePath'  => Yii::getPathOfAlias('application.modules.groups'),
	'messagePath' => Yii::getPathOfAlias('application.modules.groups.messages'),
	'languages'   => array('ru'),
	'fileTypes'   => array(
		'php',
		'html'
	),
	'exclude'     => array(),
	'translator'  => 'Yii::t',
);