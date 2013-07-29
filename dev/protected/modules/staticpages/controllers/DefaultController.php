<?php

class DefaultController extends Controller {
	public function filters () {
		//return CMap::mergeArray(parent::filters(),
		return array(
			array('application.modules.auth.filters.AuthFilter - index'),
		);
		//);
	}

	public function actionIndex ( $view ) {
		$StaticPage = StaticPage::model()->published()->findByUrl($view);
		if ( !$StaticPage ) {
			throw new CHttpException(404);
		}

		$this->pageTitle = $StaticPage->getTitle();
		$this->breadcrumbs[] = $StaticPage->getTitle();

		$this->render('page', array('model' => $StaticPage));
	}

}
