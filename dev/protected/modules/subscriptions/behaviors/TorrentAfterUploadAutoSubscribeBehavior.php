<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 26.07.14
 * Time: 19:51
 */

namespace modules\subscriptions\behaviors;

use CActiveRecordBehavior;
use Yii;
use Subscription;

/**
 * Class TorrentAfterUploadAutoSubscribeBehavior
 * @package modules\subscriptions\behaviors
 *
 * @method \modules\torrents\models\Torrent getOwner()
 */
class TorrentAfterUploadAutoSubscribeBehavior extends CActiveRecordBehavior
{

    public function afterSave($event)
    {
        /**
         * Автоподписка на все комментарии к группе при загрузке торрента в группу
         */
        if ($this->getOwner()->getIsNewRecord()) {
            if (!Subscription::check($this->getOwner()->torrentGroup->resolveClassName() . '_comments', $this->getOwner()->torrentGroup->getPrimaryKey())) {
                $model = new Subscription();
                $model->modelId = $this->getOwner()->torrentGroup->getPrimaryKey();
                $model->modelName = $this->getOwner()->torrentGroup->resolveClassName() . '_comments';
                $model->uId = Yii::app()->getUser()->getId();
                $model->ctime = time();
                $model->save(false);
            }
        }
    }

} 