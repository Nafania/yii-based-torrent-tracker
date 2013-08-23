<?php
return array(
	'sourcePath'  => Yii::getPathOfAlias('application.modules.user'),
	'messagePath' => Yii::getPathOfAlias('application.modules.user.messages'),
	'languages'   => array('ru'),
	'fileTypes'   => array(
		'php',
		'html'
	),
	'exclude'     => array(),
	'translator'  => 'Yii::t',
);