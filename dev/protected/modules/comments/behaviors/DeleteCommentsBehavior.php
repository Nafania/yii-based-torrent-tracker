<?php
class DeleteCommentsBehavior extends CActiveRecordBehavior {

	public function afterDelete ( $e ) {
		parent::afterDelete($e);

		$owner = $this->getOwner();

		$db = Yii::app()->getDb();
		$sql = 'DELETE FROM {{comments}} WHERE modelName = :modelName AND modelId = :modelId';
		$command = $db->createCommand($sql);
		$command->bindValue(':modelName', get_class($owner));
		$command->bindValue(':modelId', $owner->getPrimaryKey());

		$command->execute();

		return true;
	}
}