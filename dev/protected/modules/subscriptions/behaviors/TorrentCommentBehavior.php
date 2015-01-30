<?php
namespace modules\subscriptions\behaviors;

use modules\torrents\models\TorrentGroup;
use Yii;
use Subscription;

class TorrentCommentBehavior extends BaseEventBehavior
{
    public function afterSave($e)
    {
        parent::afterSave($e);

        /**
         * @var $owner \Comment
         */
        $owner = $this->getOwner();

        /**
         * Если комментарий не новый или он относится не к группе торрентов, то ничего не делаем
         */
        if (!$owner->getIsNewRecord() || $owner->modelName <> TorrentGroup::model()->resolveClassName() ) {
            return true;
        }

        $data = [];
        $url = $owner->getUrl();
        $icon = $owner->getChangesIcon();
        /**
         * @var TorrentGroup $torrentGroup
         */
        $torrentGroup = TorrentGroup::model()->findByPk($owner->modelId);


        /**
         * @var Subscription[] $subscriptions
         */
        $subscriptions = Subscription::model()->findAllByAttributes([
                'modelName' => $torrentGroup->resolveClassName() . '_comments',
                'modelId' => $torrentGroup->getId()
            ],
            ['index' => 'uId']);

        /**
         * Найдем подписки на группу торрентов, к которой относится торрент и добавим события
         */
        foreach ($subscriptions AS $subscription) {
            if ($subscription->uId == Yii::app()->getUser()->getId()) {
                continue;
            }

            $data[$subscription->uId] = [
                'text' => Yii::t('subscriptionsModule.common',
                        '{userName} добавил комментарий к группе торрентов "{torrentName}", на которую вы подписаны',
                        [
                            '{userName}' => $owner->user->getName(),
                            '{torrentName}' => $torrentGroup->getTitle(),
                        ]),

                'title' => Yii::t('subscriptionsModule.common', 'Новый комментарий'),
                'url' => $url,
                'icon' => $icon,
                'uId' => $subscription->uId,
                'uniqueType' => $icon . '_' . $torrentGroup->resolveClassName() . '_' . $torrentGroup->getPrimaryKey()
            ];
        }

        /**
         * если комментарий добавлен к конкретному торренту и автор торрента <> автору комментария, то пошлем уведомление автору торрента
         */
        if ($owner->getTorrentId() && $owner->getTorrent()->uid != Yii::app()->getUser()->getId()) {
            $data[$owner->getTorrent()->uid] = [
                'text' => Yii::t('subscriptionsModule.common',
                        '{userName} добавил комментарий к вашему торренту "{torrentName}"',
                        [
                            '{userName}' => $owner->user->getName(),
                            '{torrentName}' => $owner->getTorrent()->getTitle(),
                        ]),

                'title' => Yii::t('subscriptionsModule.common', 'Новый комментарий'),
                'url' => $url,
                'icon' => $icon,
                'uId' => $owner->getTorrent()->uid,
            ];
        }

        $this->addSubscriptionToTorrentGroup($subscriptions, $torrentGroup);

        $this->saveEvent($data);
    }

    /**
     * Метод, автоматически добавляющий подписку на новые комментарии к группе
     *
     * @param array $subscriptions
     * @param TorrentGroup $torrentGroup
     */
    protected function addSubscriptionToTorrentGroup($subscriptions, $torrentGroup)
    {
        if (!isset($subscriptions[Yii::app()->getUser()->getId()])) {
            $model = new Subscription();
            $model->modelId = $torrentGroup->getPrimaryKey();
            $model->modelName = $torrentGroup->resolveClassName() . '_comments';
            $model->uId = Yii::app()->getUser()->getId();
            $model->ctime = time();
            $model->save(false);
        }
    }
}