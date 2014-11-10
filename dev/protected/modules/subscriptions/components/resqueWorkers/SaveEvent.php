<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 25.07.14
 * Time: 15:31
 */

namespace application\modules\subscriptions\components\resqueWorkers;

use Yii;

/**
 * Class SaveEvent
 * @package application\modules\subscriptions\components\resqueWorkers
 *
 * @property array $args
 */
class SaveEvent {
    public function perform() {
        Yii::import('application.modules.subscriptions.models.*');

        /**
         * @var \Event $event
         */
        $data = $this->args['data'];

        foreach ( $data AS $_data ) {
            $event = new \Event();
            $event->setAttributes($_data, false);
            $event->save(false);
        }
    }
}