<?php
/**
 * Class BlogPostSubscription
 * Поведение для создания уведомлений тем, кто подписался на блог при создании поста в этом блоге
 */
class BlogPostSubscription extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);
		/**
		 * @var $owner modules\blogs\models\BlogPost
		 */
		$owner = $this->getOwner();
		$blog = modules\blogs\models\Blog::model()->findByPk($owner->blogId);

		if ( $blog ) {
			$subscriptions = Subscription::model()->findAllByAttributes(array(
			                                                                 'modelName' => 'Blog',
			                                                                 'modelId'   => $blog->getId()
			                                                            ));
			$url = $owner->getUrl();
			$icon = 'list';

			foreach ( $subscriptions AS $subscription ) {
				/**
				 * Если автор поста является подписчиком, то не шлем ему уведомление
				 */
				if ( $owner->ownerId == $subscription->uId ) {
					continue;
				}
				$event = new Event();
				$event->text = Yii::t('subscriptionsModule.common',
					'Добавлен новый пост в блог "{title}", за которым вы следите',
					array(
					     '{title}' => $blog->getTitle()
					));
				$event->title = Yii::t('subscriptionsModule.common', 'Новый пост в блоге');
				$event->url = $url;
				$event->icon = $icon;
				$event->uId = $subscription->uId;

				$event->save();
			}
		}
	}
}