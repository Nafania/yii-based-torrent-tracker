<?php
require_once(dirname(__FILE__) . '/protected/config/config.php');
require_once($yii);
Yii::createWebApplication($config)->run();