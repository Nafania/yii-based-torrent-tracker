<?php
/**
 * Class AddBlogSubscription
 * Поведение для создания/удаления подписки на блог группы после того, как подписываются/удаляют подписку на саму группу
 */
class AddBlogSubscription extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		$blog = $this->_returnBlogModel();

		if ( $blog ) {
			$subscription = new Subscription();
			$subscription->modelId = $blog->getId();
			$subscription->modelName = $blog->resolveClassName();
			$subscription->uId = Yii::app()->getUser()->getId();
			return $subscription->save();
		}

		return true;
	}

	public function afterDelete ( $e ) {
		$blog = $this->_returnBlogModel();

		if ( $blog ) {
			$subscriptions = Subscription::model()->findAllByAttributes(array(
			                                                                'modelId' => $blog->getId(),
			                                                                'modelName' => $blog->resolveClassName(),
			                                                                'uId' => Yii::app()->getUser()->getId(),
			                                                           ));
			foreach ( $subscriptions AS $subscription ) {
				$subscription->delete();
			}
		}

		return true;
	}

	private function _returnBlogModel () {
		$owner = $this->getOwner();

		if ( $owner->modelName == 'Group' ) {
			$group = Group::model()->findByPk($owner->modelId);
			if ( $group ) {
				$blog = Blog::model()->findByAttributes(array('groupId' => $group->getId()));

				if ( $blog ) {
					return $blog;
				}
			}
		}

		return false;
	}
}