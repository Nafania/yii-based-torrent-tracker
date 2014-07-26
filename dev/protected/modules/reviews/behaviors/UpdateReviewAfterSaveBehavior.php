<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 25.07.14
 * Time: 23:16
 */

namespace application\modules\reviews\behaviors;

use Yii;

class UpdateReviewAfterSaveBehavior extends \CActiveRecordBehavior {

    public function afterSave ($event) {
        if ( $this->getOwner()->getIsNewRecord() ) {
            Yii::app()->resque->createJob('update_reviews', 'application\modules\reviews\components\resqueWorkers\UpdateReview', ['attributes' => $this->getOwner()->attributes]);
        }
    }

} 