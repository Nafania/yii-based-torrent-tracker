<?php

class FavoritesBehavior extends \CActiveRecordBehavior {

	public function isFavorited () {
		/**
		 * @var $owner \EActiveRecord
		 */
		$owner = $this->getOwner();

		return \modules\favorites\models\Favorite::model()->findByPk(array(
			'modelId'   => $owner->getPrimaryKey(),
			'modelName' => $owner->resolveClassName(),
			'uId'       => Yii::app()->getUser()->getId(),
		));
	}

	public function afterDelete ( $e ) {
		parent::afterDelete($e);

		/**
		 * @var $owner \EActiveRecord
		 */
		$owner = $this->getOwner();

		\modules\favorites\models\Favorite::model()->deleteByPk(array(
			'modelId'   => $owner->getPrimaryKey(),
			'modelName' => $owner->resolveClassName(),
			'uId'       => Yii::app()->getUser()->getId(),
		));
	}

	public function onlyFavorites () {
		/**
		 * @var $owner \EActiveRecord
		 */
		$owner = $this->getOwner();

		$criteria = new \CDbCriteria();
		$criteria->join = 'INNER JOIN favorites f ON ( f.modelId = t.id AND f.modelName = :modelName AND f.uId = :uId )';
		$criteria->params = array(
			':modelName' => $owner->resolveClassName(),
			':uId'       => \Yii::app()->getUser()->getId(),
		);
		$criteria->order = 'f.ctime DESC';
		$criteria->together = true;

		$owner->getDbCriteria()->mergeWith($criteria);

		return $owner;
	}
}