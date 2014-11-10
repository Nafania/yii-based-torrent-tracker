<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 20.05.14
 * Time: 13:31
 */
class WarmRedisCommand extends CConsoleCommand {
    public function actionEvents () {
        $db = Yii::app()->getDb();

        $rows = $db->createCommand('SELECT uId, count FROM events WHERE unread = 1')->queryAll();

        Yii::app()->redis->del(Event::REDIS_HASH_NAME);

        foreach ( $rows AS $row ) {
            Yii::app()->redis->hSet(Event::REDIS_HASH_NAME, $row['uId'], ( $row['count'] ? $row['count'] : 1 ) * 1);
        }
    }
}