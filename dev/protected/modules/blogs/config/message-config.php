<?php
return array(
	'sourcePath'  => Yii::getPathOfAlias('application.modules.blogs'),
	'messagePath' => Yii::getPathOfAlias('application.modules.blogs.messages'),
	'languages'   => array('ru'),
	'fileTypes'   => array(
		'php',
		'html'
	),
	'exclude'     => array(),
	'translator'  => 'Yii::t',
);