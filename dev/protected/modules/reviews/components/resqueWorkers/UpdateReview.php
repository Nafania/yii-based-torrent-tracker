<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 25.07.14
 * Time: 15:31
 */

namespace application\modules\reviews\components\resqueWorkers;

use Yii;
use EActiveRecord;

/**
 * Class UpdateReview
 * @package application\modules\reviews\components\resqueWorkers
 *
 * @property array $args
 */
class UpdateReview {
    public function perform() {
        Yii::import('application.modules.reviews.models.*');
        Yii::import('application.modules.reviews.components.*');
        Yii::import('application.modules.reviews.components.parsers.*');
        Yii::import('application.modules.reviews.ReviewsModule');

        $attributes = $this->args['attributes'];

        $sql = 'SELECT * FROM {{reviewsRelations}} WHERE cId = :cId';
        $db = Yii::app()->getDb();
        $comm = $db->createCommand($sql);

        foreach ($comm->query([':cId' => $attributes['cId']]) AS $review) {
            /**
             * @var \ReviewRelation $ReviewRelation
             */
            $ReviewRelation = EActiveRecord::model('ReviewRelation')->populateRecord($review);

            $params = $ReviewRelation->getParams();
            $class = new $ReviewRelation->apiName;

            $attrs = $params;

            $eavComm = $db->createCommand();
            $eavComm->select = 'attribute, value';
            $eavComm->from = '{{torrentGroupsEAV}}';
            $eavComm->where(['in', 'attribute', $params]);
            $eavComm->andWhere('entity = :entity', [':entity' => $attributes['id']]);

            foreach ($eavComm->query() AS $eavRow) {
                if (($key = array_search($eavRow['attribute'], $attrs)) !== false) {
                    $attrs[$key] = $eavRow['value'];
                }
            }

            $class->getReviewData($attrs, 'modules_torrents_models_TorrentGroup', $attributes['id']);
        }
    }
}