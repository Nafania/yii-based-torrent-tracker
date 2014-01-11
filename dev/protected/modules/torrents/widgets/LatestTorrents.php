<?php
class LatestTorrents extends CWidget {
	public $limit = 10;

	public function run () {
		$criteria = new CDbCriteria();
		$criteria->order = 't.seeders + t.leechers DESC, t.ctime DESC';
		$criteria->limit = $this->limit;
		$criteria->condition = 't.ctime > ( UNIX_TIMESTAMP(NOW()) - 7 * 24 * 60 * 60 )';

		$torrents = \modules\torrents\models\Torrent::model()->setSearchSettings()->findAll($criteria);

		$this->render('latestTorrents', array(
			'torrents' => $torrents
		));
	}
}