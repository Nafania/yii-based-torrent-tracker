<?php
/**
 * Created by PhpStorm.
 * User: nafania
 * Date: 15.01.2015
 * Time: 12:54
 */

namespace modules\autotags\behaviors;

class AutoTag extends \CActiveRecordBehavior {
    public function beforeSave($e)
    {
        parent::beforeSave($e);

        /**
         * @var \modules\torrents\models\Torrent $owner
         */
        $owner = $this->getOwner();

        if ( !$owner->getIsNewRecord() ) {
            return true;
        }

        $group = $owner->torrentGroup;
        $categoryPk = $group->category->getPrimaryKey();

        $rows = \Yii::app()->getDb()->createCommand('SELECT t.name FROM auto_tag at, tags t WHERE at.fk_tag = t.id AND at.fk_category = '. $categoryPk)->queryAll();

        foreach ( $rows AS $row ) {
            $owner->addTag($row['name']);
            $group->addTag($row['name']);
        }

        return true;
    }
}