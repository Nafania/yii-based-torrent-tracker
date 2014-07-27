<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 27.07.14
 * Time: 20:44
 */
class UpdateTorrentGroupSubscriptionsCommand extends CConsoleCommand {
    public function actionIndex () {
        //сначала возьмем всех аплоадеров
        $comm = Yii::app()->db->createCommand('SELECT gid, uid FROM {{torrents}} WHERE uid IS NOT NULL');

        foreach ( $comm->query() AS $row ) {
            Yii::app()->db->createCommand('INSERT IGNORE INTO subscriptions (modelId, modelName, uId, ctime) VALUES (:modelId, :modelName, :uId, :ctime)')->execute([
                ':modelId' => $row['gid'],
                ':modelName' => 'modules_torrents_models_TorrentGroup_comments',
                ':uId' => $row['uid'],
                ':ctime' => time()
            ]);
        }

        //затем комментаторов
        $comm = Yii::app()->db->createCommand('SELECT ownerId, modelId FROM comments WHERE modelName = \'modules_torrents_models_TorrentGroup\' AND ownerId IS NOT NULL');

        foreach ( $comm->query() AS $row ) {
            Yii::app()->db->createCommand('INSERT IGNORE INTO subscriptions (modelId, modelName, uId, ctime) VALUES (:modelId, :modelName, :uId, :ctime)')->execute([
                ':modelId' => $row['modelId'],
                ':modelName' => 'modules_torrents_models_TorrentGroup_comments',
                ':uId' => $row['ownerId'],
                ':ctime' => time()
            ]);
        }
    }
}