<?php

class ReviewWidget extends CWidget
{
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

        $reviews = Review::model()->findAllByAttributes(array(
                'modelId' => $this->model->getPrimaryKey(),
                'modelName' => $this->model->resolveClassName(),
            ),
            'ratingText <> ""');

        foreach ($reviews AS $review) {
            $class = new $review->apiName;
            echo str_replace(array(
                    '{ratingTitle}',
                    '{ratingValue}'
                ),
                array(
                    $class->getDescription(),
                    $review->ratingText,
                ),
                $this->template);
        }
    }
}