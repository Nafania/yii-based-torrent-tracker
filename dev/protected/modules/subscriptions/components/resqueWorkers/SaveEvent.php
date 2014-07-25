<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 25.07.14
 * Time: 15:31
 */

namespace modules\subscriptions\components\resqueWorkers;

/**
 * Class SaveEvent
 * @package modules\subscriptions\components\resqueWorkers
 *
 * @property array $args
 */
class SaveEvent {
    public function perform() {
        /**
         * @var \Event $event
         */
        $event = $this->args['event'];

        $event->save();
    }
}