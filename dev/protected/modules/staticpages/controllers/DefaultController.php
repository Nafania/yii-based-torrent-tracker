<?php

class DefaultController extends components\Controller {

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
