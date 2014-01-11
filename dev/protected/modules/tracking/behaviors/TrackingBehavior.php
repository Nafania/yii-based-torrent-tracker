<?php
/**
 * Class TrackingBehavior
 */
class TrackingBehavior extends CActiveRecordBehavior {
	const TRACKING_KEY = 'tracking';

	/**
	 * @return bool
	 */
	public function getIsNew () {
		$isNew = $this->_isNew();

		$this->addTracking($this->getOwner()->getPrimaryKey());

		return $isNew;
	}

	private function _isNew () {
		$owner = $this->getOwner();

		return !$this->hasTracking($owner->getPrimaryKey()) && ($owner->getLastTime() > Yii::app()->getUser()->getLastVisitTime());
	}

	/**
	 * @param CEvent $e
	 */
	/*public function afterFind ( $e ) {
		parent::afterFind($e);

		$this->addTracking($this->getOwner()->getPrimaryKey());
	}*/

	/**
	 * @param $pk
	 *
	 * @return bool
	 */
	protected function hasTracking ( $pk ) {
		$tracking = $this->getTracking();

		return in_array($pk, $tracking);
	}

	/**
	 * @param $pk
	 */
	protected function addTracking ( $pk ) {
		if ( $this->_isNew() ) {
			$tracking = $this->getTracking();
			$tracking[] = $pk;

			Yii::app()->getUser()->setState($this->getTrackingKey(), $tracking);
		}
	}

	/**
	 * @param $val
	 */
	protected function setTracking ( $val ) {
		Yii::app()->getUser()->setState($this->getTrackingKey(), $val);
	}

	/**
	 * @return array|mixed
	 */
	protected function getTracking () {
		$tracking = Yii::app()->getUser()->getState($this->getTrackingKey());

		if ( !is_array($tracking) ) {
			return array();
		}

		return $tracking;
	}

	/**
	 * @return string
	 */
	public function getTrackingKey () {
		/**
		 * @var $owner EActiveRecord
		 */
		$owner = $this->getOwner();

		return self::TRACKING_KEY . '_' . $owner->resolveClassName();
	}
}