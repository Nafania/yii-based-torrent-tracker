<?php
namespace modules\subscriptions\behaviors;

use Yii;
use Subscription;
use modules\blogs\models\Blog;

/**
 * Class BlogPostSubscription
 * Поведение для создания уведомлений тем, кто подписался на блог при создании поста в этом блоге
 */
class BlogPostSubscription extends BaseEventBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);
		/**
		 * @var $owner BlogPost
		 */
		$owner = $this->getOwner();
		$blog = Blog::model()->findByPk($owner->blogId);

		if ( $owner->getIsNewRecord() && $blog ) {
			$subscriptions = Subscription::model()->findAllByAttributes(array(
			                                                                 'modelName' => 'modules_blogs_models_Blog',
			                                                                 'modelId'   => $blog->getId()
			                                                            ));
			$url = $owner->getUrl();
			$icon = 'list';

            $data = [];

			foreach ( $subscriptions AS $subscription ) {
				/**
				 * Если автор поста является подписчиком, то не шлем ему уведомление
				 */
				if ( $owner->ownerId == $subscription->uId ) {
					continue;
				}

                $data[] = [

                    'text' => Yii::t('subscriptionsModule.common',
                            'Добавлен новый пост в блог "{title}", за которым вы следите',
                            array(
                                '{title}' => $blog->getTitle()
                            )),
                    'title' => Yii::t('subscriptionsModule.common', 'Новый пост в блоге'),
                    'url' => $url,
                    'icon' => $icon,
                    'uId' => $subscription->uId,
                ];
			}

            $this->saveEvent($data);
		}
	}
}