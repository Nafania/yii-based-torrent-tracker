<?php
/**
 * @method Comment getOwner()
 */
class BlogCommentBehavior extends BaseEventBehavior
{
    public function afterSave($e)
    {
        parent::afterSave($e);

        $owner = $this->getOwner();

        if ($owner->modelName != 'modules_blogs_models_BlogPost') {
            return false;
        }

        /**
         * @var modules\blogs\models\BlogPost $blogPost
         */
        $blogPost = modules\blogs\models\BlogPost::model()->findByPk($owner->modelId);
        if (!$blogPost) {
            return false;
        }

        if ($owner->getIsNewRecord() && $blogPost) {
            $subscriptions = Subscription::model()->findAllByAttributes(array(
                'modelName' => 'modules_blogs_models_BlogPost',
                'modelId' => $blogPost->getId()
            ));

            $url = $owner->getUrl();
            $icon = 'comment';

            foreach ($subscriptions AS $subscription) {

                if ( $owner->ownerId == $subscription->uId ) {
                    continue;
                }

                $event = new Event();
                $event->text = Yii::t('subscriptionsModule.common',
                    'Добавлен новый комментарий к записи "{title}"',
                    array(
                        '{title}' => $blogPost->getTitle()
                    ));
                $event->title = Yii::t('subscriptionsModule.common', 'Новый комменатрий к записи');
                $event->url = $url;
                $event->icon = $icon;
                $event->uId = $subscription->uId;
                $event->uniqueType = $icon . $owner->modelName . $blogPost->getPrimaryKey();

                $this->saveEvent($event);
            }
        }
        return false;
    }
}