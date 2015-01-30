<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 25.07.14
 * Time: 15:26
 */
namespace modules\subscriptions\behaviors;

use Yii;
use CActiveRecordBehavior;

class BaseEventBehavior extends CActiveRecordBehavior {
    protected function saveEvent ( $eventData ) {
        /**
         * Если массив не ассоциативный
         */
        if (!array_key_exists(0, $eventData)) {
            $eventData = [$eventData];
        }
        Yii::import('application.modules.subscriptions.models.*');

        /**
         * @var \Event $event
         */
        Yii::app()->resque->createJob('save_events', 'application\modules\subscriptions\components\resqueWorkers\SaveEvent', ['data' => $eventData]);
    }
}