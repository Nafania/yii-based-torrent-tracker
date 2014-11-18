<?php
class GetTorrentTitleBehavior extends CActiveRecordBehavior {
	/**
	 * Используется в случае генерации title в beforeSave для того, чтобы не было повторного сохранения данных
	 * @var bool
	 */
	private $_beforeSaveCalled = false;

	public function getTitle () {
        /**
         * @var modules\torrents\models\Torrent $owner
         */
        $owner = $this->getOwner();

		if ( $title = $owner->title ) {
			return $title;
		}

		$attributes = $this->getTitleAttributes();

		$return = array();
		foreach ( $attributes AS $row ) {
			$prepend  = ( $row['prepend'] ? $row['prepend'] . ' ' : '' );
			$append  = ( $row['append'] ? ' ' . $row['append'] : '' );

			$val = $owner->getEavAttribute($row['attrId']);
			if ( $val ) {
				$return[] = $prepend . $val . $append;
			}
		}

		$return = array_unique($return);
		$return = implode(' ' . Yii::app()->config->get('torrentsModule.torrentsNameDelimiter') . ' ', $return);

        $owner->title = $return;

		if ( !$this->_beforeSaveCalled && $owner->title ) {
            $owner->save(false);
		}

		return $return;
	}

	public function beforeSave($e) {
		parent::beforeSave($e);

		$this->_beforeSaveCalled = true;
		$this->getTitle();
		$this->_beforeSaveCalled = false;
	}

	public function getTitleAttributes () {
		$connection = $this->getOwner()->getDbConnection();

		$sql = 'SELECT t.attrId, a.* FROM {{torrentsNameRules}} t, {{attributes}} a WHERE t.catId = :catId AND t.attrId = a.id ORDER BY t.`order` ASC';
		$command = $connection->createCommand($sql);
		$command->bindValue(':catId', $this->getOwner()->category->getId());
		$dataReader = $command->query();

		$return = array();
		while ( ($row = $dataReader->read()) !== false ) {
			$return[] = $row;
		}

		return $return;
	}
}