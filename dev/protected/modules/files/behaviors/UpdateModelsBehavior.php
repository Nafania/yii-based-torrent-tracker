<?php
class UpdateModelsBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		if ( !$files = Yii::app()->getUser()->getState(File::STATE_NAME) ) {
			return true;
		}

		$db = Yii::app()->getDb();
		$id = $this->getOwner()->getPrimaryKey();
		$modelName = get_class($this->getOwner());

		foreach ( $files AS $file ) {
			$sql = 'UPDATE {{files}} SET modelId = :id WHERE title = :title AND modelName = :modelName AND ownerId = :ownerId';
			$command = $db->createCommand($sql);
			$command->bindValue(':id', $id);
			$command->bindValue(':title', $file['title']);
			$command->bindValue(':modelName', $modelName);
			$command->bindValue(':ownerId', Yii::app()->getUser()->getId());
			$command->execute();
		}

		Yii::app()->getUser()->setState(File::STATE_NAME, null);

		return true;
	}
}