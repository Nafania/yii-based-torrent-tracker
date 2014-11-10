<?php

class LatestComments extends CWidget {
	public $limit = 10;

	public function run () {
		$criteria = new CDbCriteria();
		$criteria->limit = $this->limit;
		$criteria->order = 't.ctime DESC';
		$criteria->condition = 't.modelName = :modelName';
		$criteria->params = array(
			':modelName' => \modules\torrents\models\TorrentGroup::model()->resolveClassName(),
		);

		$comments = Comment::model()->findAll($criteria);

		$this->render('latestComments',
			array(
				'comments' => $comments
			));
	}
}