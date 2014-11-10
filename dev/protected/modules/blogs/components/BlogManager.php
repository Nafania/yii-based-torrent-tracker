<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 14.10.2014
 * Time: 11:09
 */
namespace modules\blogs\components;

use modules\blogs\models\Blog;
use CComponent;
use Yii;

class BlogManager extends CComponent {
    public function init() {
    }

    public function getPostsCount( Blog $model, $onlyVisible = true ) {
        return Yii::app()->getDb()->createCommand('SELECT COUNT(*) FROM {{blogPosts}} bp, {{blogs}} b WHERE bp.blogId = b.id AND b.id = :pk' . ( $onlyVisible ? ' AND bp.hidden = 0' : ''))->queryScalar([':pk' => $model->getPrimaryKey()]);
    }
}