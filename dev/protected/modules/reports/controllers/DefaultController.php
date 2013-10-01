<?php

class DefaultController extends Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + create'));
	}

	public function actionCreate () {
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);

		$this->pageTitle = Yii::t('reportsModule.common', 'Создание жалобы');
		$this->breadcrumbs[] = Yii::t('reportsModule.common', 'Создание жалобы');

		if ( !class_exists($modelName) ) {
			throw new CHttpException(404, Yii::t('reportsModule.common', 'Данные не существуют'));
		}
		$model = $modelName::model()->findByPk($modelId);

		if ( !$model ) {
			throw new CHttpException(404, Yii::t('reportsModule.common', 'Указанные данные не найдены'));
		}

		$reportContent = new ReportContent();

		$reportContent->modelId = $modelId;
		$reportContent->modelName = $modelName;

		$this->performAjaxValidation($reportContent);

		if ( isset($_POST['ReportContent']) ) {
			$reportContent->attributes = $_POST['ReportContent'];

			if ( $reportContent->save() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('reportsModule.common', 'Report sent successfully'));
			}
			else {
				Ajax::send(Ajax::AJAX_WARNING, Yii::t('reportsModule.common', 'Some errors occurred during save'));
			}
		}

		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		$view = $this->renderPartial('create',
			array(
			     'report' => $reportContent,
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