<?php

class ReviewWidget extends CWidget
{
    /**
     * @var $model EActiveRecord
     */
    public $model;
    public $template;

    public function init()
    {
        parent::init();
    }

    public function run()
    {

        Yii::import('application.modules.reviews.components.parsers.*');
        Yii::import('application.modules.reviews.models.*');

        /**
         * @var $reviews Review[]
         */
        $reviews = Review::model()->findAllByAttributes(array(
                'modelId' => $this->model->getPrimaryKey(),
                'modelName' => $this->model->resolveClassName(),
            ), 'params IS NOT NULL');

        foreach ($reviews AS $review) {
            /**
             * @var $class ReviewInterface
             */
            $class = new $review->apiName;

            echo str_replace(array(
                    '{ratingTitle}',
                    '{ratingValue}'
                ),
                array(
                    $class->getDescription(),
                    $class->returnReviewString(CJSON::decode($review->params)),
                ),
                $this->template);
        }
    }
}