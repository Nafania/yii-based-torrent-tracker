<?php

class ReviewsBackendController extends YAdminController {

	public function filters () {
		return parent::filters();
	}

	public function init () {
		parent::init();
	}

	public function actionIndex () {
		$parsers = array();
		$files = CFileHelper::findFiles(Yii::getPathOfAlias('application.modules.reviews.components.parsers'));
		Yii::import('application.modules.reviews.components.parsers.*');

		foreach ( $files AS $file ) {
			$className = pathinfo($file, PATHINFO_FILENAME);
			$class = new $className;
			$parsers[] = $class;
		}

		$categories = Category::model()->findAll();

		$this->render('index',
			array(
				'parsers' => $parsers,
				'categories' => $categories
			));
	}
}