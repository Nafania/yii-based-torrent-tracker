<?php

class ReportsBackendController extends YAdminController {

	public function filters () {
		return CMap::mergeArray(array(
		                             'postOnly + delete',
		                        ),
			parent::filters());
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete ( $id ) {
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if ( !isset($_GET['ajax']) ) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex () {
		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$this->breadcrumbs[] = Yii::t('reportsModule.common', 'Управление жалобами');
		$this->pageTitle = Yii::t('reportsModule.common', 'Управление жалобами');

		$model = new Report('search');
		$model->unsetAttributes();

		if ( isset($_GET['Report']) ) {
			$model->attributes = $_GET['Report'];
		}

		Ajax::renderAjax('index', array(
		                               'model' => $model,
		                          ));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer $id the ID of the model to be loaded
	 *
	 * @return reports the loaded model
	 * @throws CHttpException
	 */
	public function loadModel ( $id ) {
		$model = Report::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param reports $model the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'reports-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
