<?php

class DefaultController extends Controller {
	public function filters () {
		return array('suggest +ajaxOnly');
	}

	public function actionSuggest ( $q ) {
		$db = Yii::app()->getDb();

		$sql = 'SELECT * FROM {{tags}} WHERE name LIKE \'%:name%\'';
		$command = $db->createCommand($sql);
		$command->bindValue(':name', $q);
		$tags = $command->queryAll();

		$result = array();
		foreach ( $tags AS $tag ) {
			$result[] = array(
				'name' => $tag['name'],
			);
		}
		Ajax::send(Ajax::AJAX_SUCCESS, 'ok', $result);
	}
}