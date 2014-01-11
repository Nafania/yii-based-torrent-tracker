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

		$db = Yii::app()->getDb();
		$sql = 'SELECT * FROM {{reviewsRelations}} WHERE cId = :cId';
		$comm = $db->createCommand($sql);
		$comm->bindValue(':cId', $this->categoryId);

		$dataReader = $comm->query();

		foreach ( $dataReader AS $row ) {
			$params = unserialize($row['params']);
			$class = new $row['apiName'];

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