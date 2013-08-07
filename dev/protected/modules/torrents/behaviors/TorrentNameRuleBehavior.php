<?php
class TorrentNameRuleBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		if ( sizeof($this->getOwner()->torrentsNameRules) ) {
			$connection = Yii::app()->db;
			$sql = 'DELETE FROM {{torrentsNameRules}} WHERE catId = :catId';
			$command = $connection->createCommand($sql);
			$command->bindValue('catId', $this->getOwner()->getId());

			$command->execute();

			foreach ( $this->getOwner()->torrentsNameRules AS $key => $val ) {
				$sql = 'INSERT INTO {{torrentsNameRules}} (attrId, catId, `order`) VALUES(:attrId, :catId, :order)';
				$command = $connection->createCommand($sql);

				$command->bindValue('attrId', $val);
				$command->bindValue('catId', $this->getOwner()->getId());
				$command->bindValue('order', $key);

				$command->execute();
			}

		}
		return true;
	}

	public function afterDelete () {
		$connection = Yii::app()->db;
		$sql = 'DELETE FROM {{torrentsNameRules}} WHERE catId = :catId';
		$command = $connection->createCommand($sql);
		$command->bindValue('catId', $this->getOwner()->getId());
		$command->execute();
	}

	public function getTorrentsNameRules () {

		$this->torrentsNameRules = array();

		$connection = Yii::app()->db;

		$sql = 'SELECT * FROM {{torrentsNameRules}} WHERE catId = :catId ORDER BY `order` ASC';
		$command = $connection->createCommand($sql);
		$command->bindValue('catId', $this->getOwner()->getId());
		$dataReader = $command->query();

		while ( ($row = $dataReader->read()) !== false ) {
			$this->torrentsNameRules[] = $row['attrId'];
		}

		return $this->torrentsNameRules;
	}

	public function setTorrentsNameRules ( $value ) {
		$this->torrentsNameRules = $value;
	}
}