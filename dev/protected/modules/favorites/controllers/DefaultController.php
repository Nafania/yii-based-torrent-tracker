<?php
namespace modules\favorites\controllers;

use Yii;
use CMap;
use Ajax;
use CException;
use CLogger;
use CHttpException;
use modules\favorites\models\Favorite AS Favorite;

class DefaultController extends \components\Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
		return CMap::mergeArray(parent::filters(),
			array(
				'ajaxOnly + create, delete',
			));
	}

	public function actionCreate () {
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');

		try {
			$favorite = new Favorite();
			$favorite->modelId = $modelId;
			$favorite->modelName = $modelName;
			if ( $favorite->save() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('favoritesModule.common', 'Избранное добавлено успешно'));
			}
			else {
				Ajax::send(Ajax::AJAX_ERROR,
					Yii::t('favoritesModule.common', 'При добавлении в избранное произошли ошибки'));
			}
		} catch ( CException $e ) {
			Yii::log($e->getMessage(), CLogger::LEVEL_WARNING);
			Ajax::send(Ajax::AJAX_ERROR,
				Yii::t('favoritesModule.common', 'При добавлении в избранное произошли ошибки'));

		}
	}

	public function actionDelete () {
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');

		$favorite = Favorite::model()->findByPk(array(
			'modelId'   => $modelId,
			'modelName' => $modelName,
			'uId'       => Yii::app()->getUser()->getId()
		));
		if ( $favorite ) {
			if ( $favorite->delete() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('favoritesModule.common', 'Избранное удалено успешно'));
			}
			else {
				Ajax::send(Ajax::AJAX_WARNING,
					Yii::t('favoritesModule.common', 'При удалении из избранного возникли ошибки'));
			}
		}
		else {
			throw new CHttpException(404, Yii::t('favoritesModule.common', 'Избранное не найдено'));
		}
	}

	public function actionIndex () {
		$title = Yii::t('favoritesModule.common', 'Избранное');
		$this->pageTitle = $title;
		$this->breadcrumbs[] = $title;

		$model = new \modules\torrents\models\TorrentGroup();
		$model->cacheTime = 0;

		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$dataProvider = $model->onlyFavorites()->search();

		\Ajax::renderAjax('application.modules.torrents.views.default.index',
			array(
				'dataProvider' => $dataProvider,
			),
			false,
			false,
			true);
	}
}
