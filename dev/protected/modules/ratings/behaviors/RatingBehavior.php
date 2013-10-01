<?php
class RatingBehavior extends CActiveRecordBehavior {
	/**
	 * @method CActiveRecord getOwner()
	 */

	public function getRating () {
		$owner = $this->getOwner();
		$rating = Rating::model()->findByPk(array(
		                                         'modelName' => get_class($owner),
		                                         'modelId'   => $owner->primaryKey
		                                    ));

		if ( $rating ) {
			return $rating->getRating();
		}
		else {
			return 0;
		}
	}

	public function saveRating ( $ratingVal ) {
		$owner = $this->getOwner();

		$rating = Rating::model()->findByPk(array(
		                                         'modelName' => get_class($owner),
		                                         'modelId'   => $owner->getPrimaryKey()
		                                    ));
		if ( !$rating ) {
			$rating = new Rating();
			$rating->modelId = $owner->getPrimaryKey();
			$rating->modelName = get_class($owner);
		}
		$rating->rating = $ratingVal;
		$rating->save();
	}

	public function afterDelete ( $e ) {
		parent::afterDelete($e);

		$owner = $this->getOwner();

		$db = Yii::app()->getDb();
		$sql = 'DELETE FROM {{ratings}} WHERE modelName = :modelName AND modelId = :modelId';
		$command = $db->createCommand($sql);
		$command->bindValue(':modelName', get_class($owner));
		$command->bindValue(':modelId', $owner->getPrimaryKey());

		$command->execute();

		$sql = 'DELETE FROM {{ratingRelations}} WHERE modelName = :modelName AND modelId = :modelId';
		$command = $db->createCommand($sql);
		$command->bindValue(':modelName', get_class($owner));
		$command->bindValue(':modelId', $owner->getPrimaryKey());

		$command->execute();

		return true;
	}


	public function afterFind ( $e ) {
		parent::afterFind($e);
		/**
		 * проверяем, нужно ли обновить кеш рейтинга
		 */
		if ( Yii::app()->cache->get(get_class($this->getOwner()) . $this->getOwner()->getPrimaryKey() . 'RatingUpdateTime') === false ) {

			/**
			 * пересчитываем рейтинг
			 */
			$this->calculateRating();

			/**
			 * обнуляем кеш модели, чтобы брались пересчитанные данные рейтинга
			 */
			Yii::app()->cache->set($this->getOwner()->getCacheKey(), microtime(true), 0);

			/**
			 * сохраняем текущее время в кеш, на $this->ratingCacheTime секунд
			 */
			Yii::app()->cache->set(get_class($this->getOwner()) . $this->getOwner()->getPrimaryKey() . 'RatingUpdateTime',
				microtime(true),
				$this->ratingCacheTime);
		}
	}
}