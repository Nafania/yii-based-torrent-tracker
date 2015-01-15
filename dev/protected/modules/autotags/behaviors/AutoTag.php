<?php
/**
 * Created by PhpStorm.
 * User: nafania
 * Date: 15.01.2015
 * Time: 12:54
 */

namespace modules\autotags\behaviors;

class AutoTag extends \CActiveRecordBehavior {
    public function afterSave($e)
    {
        parent::afterSave($e);

        /**
         * @var \modules\torrents\models\Torrent $owner
         */
        $owner = $this->getOwner();

        if ( !$owner->getIsNewRecord() ) {
            return true;
        }

        $group = $owner->torrentGroup;
        $categoryPk = $group->category->getPrimaryKey();

        $rows = \Yii::app()->getDb()->createCommand('SELECT fk_tag FROM auto_tag WHERE fk_category = '. $categoryPk)->queryAll();

        foreach ( $rows AS $row ) {
            \Yii::app()->getDb()->createCommand('INSERT INTO tagRelations (modelId, tagId, modelName, uId) VALUES(:modelId, :tagId, :modelName, :uId)')->execute([
                ':modelId' => $owner->getPrimaryKey(),
                ':tagId' => $row['fk_tag'],
                ':modelName' => $owner->resolveClassName(),
                ':uId' => $owner->user->getPrimaryKey(),
            ]);

            if ( !\Yii::app()->getDb()->createCommand('SELECT 1 FROM tagRelations WHERE modelId = :modelId AND tagId = :tagId AND modelName = :modelName')->queryColumn([
                ':modelId' => $group->getPrimaryKey(),
                ':tagId' => $row['fk_tag'],
                ':modelName' => $group->resolveClassName(),
            ]) ) {
                \Yii::app()->getDb()->createCommand(
                    'INSERT INTO tagRelations (modelId, tagId, modelName, uId) VALUES(:modelId, :tagId, :modelName, :uId)'
                )->execute(
                    [
                        ':modelId' => $group->getPrimaryKey(),
                        ':tagId' => $row['fk_tag'],
                        ':modelName' => $group->resolveClassName(),
                        ':uId' => $owner->user->getPrimaryKey(),
                    ]
                );
            }
        }

        return true;
    }
}