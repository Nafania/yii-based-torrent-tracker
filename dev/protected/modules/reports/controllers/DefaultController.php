<?php

class DefaultController extends Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + create'));
	}

	public function actionCreate ( $modelName, $modelId ) {
		Yii::import('application.modules.' . strtolower($modelName) . 's.models.*');
		$model = $modelName::model()->findByPk($modelId);

		$this->pageTitle = Yii::t('reportsModule.common', 'Создание жалобы');
		$this->breadcrumbs[] = Yii::t('reportsModule.common', 'Создание жалобы');

		$Report = new Report();
		$Report->modelId = $modelId;
		$Report->modelName = $modelName;

		$this->performAjaxValidation($Report);

		if ( isset($_POST['Report']) ) {
			$Report->attributes = $_POST['Report'];

			if ( $Report->save() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('reportsModule.common', 'Report sent successfully'));
			}
			else {
				Ajax::send(Ajax::AJAX_WARNING, Yii::t('reportsModule.common', 'Some errors occurred during save'));
			}
		}

		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		$view = $this->renderPartial('create',
			array(
			     'report' => $Report,
			     'model'  => $model
			),
			true,
			true);

		Ajax::send(Ajax::AJAX_SUCCESS, 'ok', array('view' => $view));
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'report-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}