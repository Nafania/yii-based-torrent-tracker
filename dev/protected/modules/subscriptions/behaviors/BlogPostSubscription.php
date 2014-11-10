<?php
namespace modules\subscriptions\behaviors;

use Yii;
use Subscription;
use modules\blogs\models\Blog;

/**
 * Class BlogPostSubscription
 * Поведение для создания уведомлений тем, кто подписался на блог при создании поста в этом блоге
 */
class BlogPostSubscription extends BaseEventBehavior
{
    public function afterSave($e)
    {
        parent::afterSave($e);
        /**
         * @var \modules\blogs\models\BlogPost $owner
         * @var \modules\blogs\models\Blog $blog
         * @var Subscription[] $subscriptions
         */
        $owner = $this->getOwner();
        $blog = Blog::model()->findByPk($owner->blogId);

        if ($owner->getIsNewRecord() && $blog) {
            $subscriptions = Subscription::model()->findAllByAttributes([
                'modelName' => $blog->resolveClassName(),
                'modelId' => $blog->getId()
            ]);
            $url = $owner->getUrl();
            $icon = 'list';

            $data = [];

            foreach ($subscriptions AS $subscription) {
                /**
                 * Если автор поста является подписчиком, то не шлем ему уведомление
                 */
                if ($owner->ownerId == $subscription->uId) {
                    continue;
                }

                $data[] = [
                    'text' => Yii::t('subscriptionsModule.common',
                            'Добавлен новый пост в блог "{title}", за которым вы следите',
                            [
                                '{title}' => $blog->getTitle()
                            ]),
                    'title' => Yii::t('subscriptionsModule.common', 'Новый пост в блоге'),
                    'url' => $url,
                    'icon' => $icon,
                    'uId' => $subscription->uId,
                    'uniqueType' => $owner->resolveClassName() . $owner->getPrimaryKey(),
                ];
            }

            $this->saveEvent($data);
        }
    }

    public function afterDelete($e)
    {
        parent::afterDelete($e);
        /**
         * @var \modules\blogs\models\BlogPost $owner
         * @var \modules\blogs\models\Blog $blog
         */
        $owner = $this->getOwner();
        $blog = Blog::model()->findByPk($owner->blogId);

        Yii::app()->db->createCommand('DELETE FROM {{subscriptions}} WHERE modelName = :modelName AND modelId = :modelId')->execute(
            [
                'modelName' => $blog->resolveClassName(),
                'modelId' => $blog->getId()
            ]
        );
    }
}