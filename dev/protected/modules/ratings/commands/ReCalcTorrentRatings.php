<?php
class ReCalcRatingsCommand extends CConsoleCommand {
	public function run ( $args ) {
		$type = (isset($args[0]) ? $args[0] : '');

		switch ( $type ) {
			default:
			case 'torrents':
				$model = TorrentGroup::model();
				$count = TorrentGroup::model()->count();
				break;
		}

		$limit = 100;

		echo "calc start at " . time() . " \n";

		$criteria = new CDbCriteria();
		$criteria->order = 'ctime DESC';
		for ( $i = 0; $i < $count / $limit; ++$i ) {
			$criteria->limit = $i * $limit . ', ' . $limit;

			$models = $model->findAll($criteria);

			foreach ( $models AS $model ) {
				$model->calculateRating();
			}
			echo $i * $limit . " from " . $count . " done\n";
			unset($models);
		}
		echo "calc end at " . time() . " \n";

		exit(0);
	}
}