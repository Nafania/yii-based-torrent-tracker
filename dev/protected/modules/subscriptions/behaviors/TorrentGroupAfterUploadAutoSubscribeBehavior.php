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
 * Class TorrentGroupAfterUploadAutoSubscribeBehavior
 * @package modules\subscriptions\behaviors
 *
 * @method \modules\torrents\models\TorrentGroup getOwner()
 */
class TorrentGroupAfterUploadAutoSubscribeBehavior extends CActiveRecordBehavior
{

    public function afterSave($event)
    {
        /**
         * Автоподписка на все комментарии к группе
         */
        if ($this->getOwner()->getIsNewRecord()) {
            $model = new Subscription();
            $model->modelId = $this->getOwner()->getPrimaryKey();
            $model->modelName = $this->getOwner()->resolveClassName() . '_comments';
            $model->uId = Yii::app()->getUser()->getId();
            $model->ctime = time();
            $model->save(false);
        }
    }

} 