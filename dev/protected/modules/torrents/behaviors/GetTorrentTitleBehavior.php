<?php
class GetTorrentTitleBehavior extends CActiveRecordBehavior {
	public function getTitle () {
		if ( $title = $this->getOwner()->title ) {
			return $title;
		}

		$connection = Yii::app()->db;

		$sql = 'SELECT t.attrId, a.* FROM {{torrentsNameRules}} t, {{attributes}} a WHERE t.catId = :catId AND t.attrId = a.id ORDER BY t.`order` ASC';
		$command = $connection->createCommand($sql);
		$command->bindValue(':catId', $this->getOwner()->category->getId());
		$dataReader = $command->query();

		$return = array();
		while ( ($row = $dataReader->read()) !== false ) {
			$prepend  = ( $row['prepend'] ? $row['prepend'] . ' ' : '' );
			$append  = ( $row['append'] ? ' ' . $row['append'] : '' );

			$val = $this->getOwner()->getEavAttribute($row['attrId']);
			if ( $val ) {
				$return[] = $prepend . $val . $append;
			}
		}

		$return = array_unique($return);
		$return = implode(' ' . Yii::app()->config->get('torrentsModule.torrentsNameDelimiter') . ' ', $return);

		$this->getOwner()->title = $return;
		$this->getOwner()->save();

		return $return;
	}
}