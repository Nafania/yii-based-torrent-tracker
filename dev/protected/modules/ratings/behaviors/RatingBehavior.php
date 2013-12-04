<?php
class RatingBehavior extends CActiveRecordBehavior {
	/**
	 * @method CActiveRecord getOwner()
	 */

	/**
	 * @param $state
	 *
	 * @return array|bool return false if no errors or array of errors
	 */
	public function addRating ( $state = RatingRelations::RATING_STATE_PLUS ) {
		$owner = $this->getOwner();

		if ( !$owner->getPrimaryKey() ) {
			return false;
		}

		$RatingRelations = new RatingRelations();
		$RatingRelations->modelName = $owner->resolveClassName();
		$RatingRelations->modelId = $owner->getPrimaryKey();
		$RatingRelations->rating = ($state == RatingRelations::RATING_STATE_PLUS ? 1 : -1);
		$RatingRelations->state = $state;

		if ( $RatingRelations->save() ) {
			$this->calculateRating();
			return false;
		}
		else {
			return $RatingRelations->getErrors();
		}
	}

	public function getRating () {
		$owner = $this->getOwner();
		if ( !$owner->getPrimaryKey() ) {
			return false;
		}
		$rating = Rating::model()->findByPk(array(
		                                         'modelName' => $owner->resolveClassName(),
		                                         'modelId'   => $owner->getPrimaryKey()
		                                    ));

		if ( $rating ) {
			return round($rating->getRating());
		}
		else {
			return 0;
		}
	}

	public function getRatingClass () {
		$rating = $this->getRating();

		if ( $rating < 0 ) {
			return 'negative';
		}
		if ( $rating > 0 ) {
			return 'positive';
		}
		if ( $rating == 0 ) {
			return 'neutral';
		}
	}

	public function saveRating ( $ratingVal ) {
		/**
		 * @var $owner CActiveRecord
		 */
		$owner = $this->getOwner();

		/**
		 * Если идет запуск из консоли, то AR не используется, для ускорения работы
		 */
		if ( php_sapi_name() == 'cli' ) {
			$db = $owner->getDbConnection();

			$sql = 'INSERT INTO {{ratings}} (modelName, modelId, rating) VALUES(:modelName, :modelId, :rating) ON DUPLICATE KEY UPDATE rating = VALUES(rating)';
			$comm = $db->createCommand($sql);
			$comm->bindValue(':modelName', $owner->resolveClassName());
			$comm->bindValue(':modelId', $owner->getPrimaryKey());
			$comm->bindValue(':rating', $ratingVal);
			$comm->execute();
		}
		/**
		 * Иначе используем AR, чтобы корректно сбрасывать кеш
		 */
		else {
			$rating = Rating::model()->findByPk(array(
			                                         'modelName' => $owner->resolveClassName(),
			                                         'modelId'   => $owner->getPrimaryKey()
			                                    ));
			if ( !$rating ) {
				$rating = new Rating();
				$rating->modelId = $owner->getPrimaryKey();
				$rating->modelName = $owner->resolveClassName();
			}
			$rating->rating = $ratingVal;
			$rating->save();
		}
	}

	public function afterDelete ( $e ) {
		parent::afterDelete($e);

		$owner = $this->getOwner();

		$db = Yii::app()->getDb();
		$sql = 'DELETE FROM {{ratings}} WHERE modelName = :modelName AND modelId = :modelId';
		$command = $db->createCommand($sql);
		$command->bindValue(':modelName', $owner->resolveClassName());
		$command->bindValue(':modelId', $owner->getPrimaryKey());

		$command->execute();

		$sql = 'DELETE FROM {{ratingRelations}} WHERE modelName = :modelName AND modelId = :modelId';
		$command = $db->createCommand($sql);
		$command->bindValue(':modelName', $owner->resolveClassName());
		$command->bindValue(':modelId', $owner->getPrimaryKey());

		$command->execute();

		return true;
	}


	public function afterFind ( $e ) {
		parent::afterFind($e);

		if ( Yii::app()->getModule('ratings')->useCronReCalc === true ) {
			return true;
		}

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