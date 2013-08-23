<?php
class TorrentCommentsRelationsBehavior extends CActiveRecordBehavior {
	private $_torrentId = false;

	//TODO: rewrite to active record

	public function attach( $owner ) {
		parent::attach($owner);
		//parent::beforeFind($e);

		//anger loading disabled due some errors with cache validation
		//$criteria = new CDbCriteria();
		//$criteria->select = 'commentId, torrentId';
		//$criteria->with = array('torrentComments', 'torrent');

		//$owner->getDbCriteria()->mergeWith($criteria);
	}

	public function getTorrentId () {
		if ( $this->getOwner()->torrentComments && $this->_torrentId = $this->getOwner()->torrentComments->torrentId ) {
			return $this->_torrentId;
		}
		if ( $this->_torrentId !== false ) {
			return $this->_torrentId;
		}
		if ( $this->getOwner()->getIsNewRecord() ) {
			return false;
		}

		return $this->_torrentId;
	}

	public function setTorrentId ( $value ) {
		$this->_torrentId = $value;
	}

	public function getTorrentGroup () {
		Yii::import('application.modules.torrents.models.*');
		if ( $this->getOwner()->torrent ) {
			return $this->getOwner()->torrent->torrentGroup;
		}
		$Torrent = Torrent::model()->findByPk($this->getTorrentId());

		return $Torrent->torrentGroup;
	}

	public function beforeSave($event) {
		parent::beforeSave($event);

		if ( $parentId = $this->getOwner()->parentId ) {
			$parentComment = Comment::model()->findByPk($parentId);
			$this->_torrentId = $parentComment->torrentId;
		}

		return true;
	}

	public function afterSave ( $event ) {
		parent::afterSave($event);
		if ( !$this->getTorrentId() ) {
			return false;
		}
		$connection = Yii::app()->getDb();
		$sql = 'INSERT {{torrentCommentsRelations}} (commentId, torrentId) VALUES(:commentId, :torrentId)';
		$command = $connection->createCommand($sql);
		$command->bindValue('commentId', $this->getOwner()->getId());
		$command->bindValue('torrentId', $this->getTorrentId());
		$command->execute();
	}

	public function afterDelete($event) {
		parent::afterDelete($event);

		$connection = Yii::app()->getDb();
		$sql = 'DELETE FROM {{torrentCommentsRelations}} WHERE commentId = :commentId AND torrentId = :torrentId';
		$command = $connection->createCommand($sql);
		$command->bindValue('commentId', $this->getOwner()->getId());
		$command->bindValue('torrentId', $this->getTorrentId());
		$command->execute();
	}

}