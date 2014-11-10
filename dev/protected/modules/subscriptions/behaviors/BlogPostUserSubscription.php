<?php

/**
 * Class BlogPostUserSubscription
 * @method \modules\blogs\models\BlogPost getOwner()
 */
class BlogPostUserSubscription extends CActiveRecordBehavior {
    /**
     * @param CModelEvent $e
     * @return bool|void
     */
    public function afterSave ( $e ) {
		parent::afterSave($e);

		$owner = $this->getOwner();

		if ( $owner->getIsNewRecord() ) {
			$subscription = new Subscription();
			$subscription->modelId = $owner->getId();
			$subscription->modelName = $owner->resolveClassName();
			$subscription->uId = $owner->ownerId;
			return $subscription->save();
		}

		return true;
	}

	public function afterDelete ( $e ) {
		parent::afterDelete($e);

		$owner = $this->getOwner();

		$subscription = Subscription::model()->findByPk(array(
		                                                     'modelId'   => $owner->getId(),
		                                                     'modelName' => $owner->resolveClassName(),
		                                                     'uId'       => $owner->ownerId
		                                                ));

		if ( $subscription ) {
			return $subscription->delete();
		}

		return true;
	}
}