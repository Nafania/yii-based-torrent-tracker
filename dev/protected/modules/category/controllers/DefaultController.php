<?php

class DefaultController extends Controller {
	public function filters () {
		//return CMap::mergeArray(parent::filters(),
		return array(
			'application.modules.auth.filters.AuthFilter - loadTree',
		);
		//);
	}

	public function actionLoadTree () {
		$tree = Category::model()->getTree();

		echo CJSON::encode($tree);
		Yii::app()->end();
	}
}