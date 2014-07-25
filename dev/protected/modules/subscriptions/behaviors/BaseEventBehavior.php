<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 25.07.14
 * Time: 15:26
 */

class BaseEventBehavior extends CActiveRecordBehavior {
    protected function saveEvent ( Event $event ) {
        Yii::app()->resque->createJob('save_events', \modules\subscriptions\components\resqueWorkers\SaveEvent, ['event' => $event]);
    }
}