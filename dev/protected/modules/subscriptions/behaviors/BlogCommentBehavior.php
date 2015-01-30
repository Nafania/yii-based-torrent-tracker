<?php
namespace modules\subscriptions\behaviors;

use modules\blogs\models\BlogPost;
use Yii;
use Subscription;

/**
 * @method \Comment getOwner()
 */
class BlogCommentBehavior extends BaseEventBehavior
{
    public function afterSave($e)
    {
        parent::afterSave($e);

        $owner = $this->getOwner();

        /**
         * Если комментарий не новый или он относится не к записи в блоге, то ничего не делаем
         */
        if (!$owner->getIsNewRecord() || $owner->modelName <> BlogPost::model()->resolveClassName() ) {
            return false;
        }
        /**
         * @var BlogPost $blogPost
         */
        $blogPost = BlogPost::model()->findByPk($owner->modelId);
        if (!$blogPost) {
            return false;
        }

        if ($blogPost) {
            /**
             * @var Subscription[] $subscriptions
             */
            $subscriptions = Subscription::model()->findAllByAttributes(array(
                'modelName' => \CHtml::modelName($blogPost),
                'modelId' => $blogPost->getId()
            ));

            $url = $owner->getUrl();
            $icon = 'comment';

            foreach ($subscriptions AS $subscription) {

                if ($owner->ownerId == $subscription->uId) {
                    continue;
                }

                $this->saveEvent([
                    'text' => Yii::t('subscriptionsModule.common',
                            'Добавлен новый комментарий к записи "{title}"',
                            array(
                                '{title}' => $blogPost->getTitle()
                            )),
                    'title' => Yii::t('subscriptionsModule.common', 'Новый комменатрий к записи'),
                    'url' => $url,
                    'icon' => $icon,
                    'uId' => $subscription->uId,
                    'uniqueType' => $icon . $owner->modelName . $blogPost->getPrimaryKey(),
                ]);
            }
        }
    }
}