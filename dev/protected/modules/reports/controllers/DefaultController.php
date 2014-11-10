<?php

class DefaultController extends components\Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + create'));
	}

	public function actionCreate () {
		/**
		 * disable all scripts
		 */
		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.min.js'] = false;
		Yii::app()->getClientScript()->scriptMap['common.js'] = false;

		$modelName = Yii::app()->getRequest()->getParam('modelName', '');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);

		$className = EActiveRecord::classNameToNamespace($modelName);

		if ( !class_exists($className) ) {
			throw new CHttpException(404, Yii::t('reportsModule.common', 'Данные не существуют'));
		}
		$model = $className::model()->findByPk($modelId);

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
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('reportsModule.common', 'Жалоба успешно отправлена.'));
			}
			else {
				Ajax::send(Ajax::AJAX_WARNING, Yii::t('reportsModule.common', 'При отправке жалобы возникли проблемы, пожалуйста, попробуйте отправить жалобу позднее.'));
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