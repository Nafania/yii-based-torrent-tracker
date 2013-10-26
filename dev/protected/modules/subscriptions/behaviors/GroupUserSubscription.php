<?php
class GroupUserSubscription extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);
		/**
		 * @var GroupUser $owner
		 */
		$owner = $this->getOwner();

		if ( $owner->getIsNewRecord() ) {
			$subscription = new Subscription();
			$subscription->modelId = $owner->idGroup;
			$subscription->modelName = 'Group';
			$subscription->uId = $owner->idUser;
			return $subscription->save();
		}

		return true;
	}

	public function afterDelete ( $e ) {
		parent::afterDelete($e);
		/**
		 * @var GroupUser $owner
		 */
		$owner = $this->getOwner();

		$subscription = Subscription::model()->findByPk(array(
		                                                     'modelId'   => $owner->idGroup,
		                                                     'modelName' => 'Group',
		                                                     'uId'       => $owner->idUser
		                                                ));

		if ( $subscription ) {
			return $subscription->delete();
		}

		return true;
	}
}