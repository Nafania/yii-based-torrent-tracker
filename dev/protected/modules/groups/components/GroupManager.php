<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 14.10.2014
 * Time: 11:09
 */
namespace modules\groups\components;

use CComponent;
use Group;
use Yii;

class GroupManager extends CComponent {
    public function init() {
    }

    public function getPostsCount( Group $model, $onlyVisible = true ) {
        return Yii::app()->getDb()->createCommand('SELECT COUNT(*) FROM {{blogPosts}} bp, {{blogs}} b WHERE bp.blogId = b.id AND b.groupId = :pk' . ( $onlyVisible ? ' AND bp.hidden = 0' : ''))->queryScalar([':pk' => $model->getPrimaryKey()]);
    }
}