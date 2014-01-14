<?php

class DeleteTempFilesCommand extends CConsoleCommand {
	public function run () {
		$criteria = new CDbCriteria();
		$criteria->condition = 'modelId = :modelId AND ctime < :ctime';
		$criteria->params = array(
			':modelId' => 0,
			':ctime'   => time() - 7 * 24 * 60 * 60,
		);


		$files = File::model()->findAll($criteria);

		$transaction = File::model()->getDbConnection()->beginTransaction();

		try {
			foreach ( $files AS $file ) {
				$file->delete();
			}

			$transaction->commit();

			return 0;
		} catch ( CException $e ) {
			$transaction->rollback();

			echo $e->getMessage();

			return 1;
		}
	}
}