<?php

class ReviewWidget extends CWidget {
	public $model;
	public $categoryId;
	public $attributes;
	public $template;

	public function init () {
		parent::init();
	}

	public function run () {

		Yii::import('application.modules.reviews.components.parsers.*');
		Yii::import('application.modules.reviews.models.*');

		$models = ReviewRelation::model()->findAllByAttributes(array('cId' => $this->categoryId));

		foreach ( $models  AS $model ) {
			$params = $model->getParams();
			$class = new $model->apiName;

			$attrs = array();
			foreach ( $params AS $key => $val ) {
				if ( !empty($this->attributes[$val]) ) {
					$attrs[$key] = $this->model->getEavAttribute($val);
				}
			}

			$ratingValue = $class->getReviewData($this->model, $attrs);

			if ( $ratingValue ) {
				echo str_replace(array(
					'{ratingTitle}',
					'{ratingValue}'
				),
					array(
						$class->getDescription(),
						$ratingValue
					),
					$this->template);
			}
		}
	}
}