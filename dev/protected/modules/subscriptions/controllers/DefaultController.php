<?php

class DefaultController extends Controller {

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
			$subscription = new Subscription();
			$subscription->modelId = $modelId;
			$subscription->modelName = $modelName;
			if ( $subscription->save() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('subscriptionsModule.common', 'Подписка добавлена успешно'));
			}
			else {
				Ajax::send(Ajax::AJAX_ERROR,
					Yii::t('subscriptionsModule.common', 'При добавлении подписки произошли ошибки'));
			}
		} catch ( CException $e ) {
			Yii::log($e->getMessage(), CLogger::LEVEL_WARNING);
			Ajax::send(Ajax::AJAX_ERROR,
				Yii::t('subscriptionsModule.common', 'При добавлении подписки произошли ошибки'));

		}
	}

	public function actionDelete () {
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');

		$subcription = Subscription::model()->findByPk(array(
		                                                    'modelId' => $modelId,
		                                                    'modelName' => $modelName,
		                                                    'uId' => Yii::app()->getUser()->getId()
		                                               ));
		if ( $subcription ) {
			if ( $subcription->delete() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('subscriptionsModule.common', 'Подписка удалена успешно'));
			}
			else {
				Ajax::send(Ajax::AJAX_WARNING, Yii::t('subscriptionsModule.common', 'При удалении подписки возникли ошибки'));
			}
		}
		else {
			throw new CHttpException(404, Yii::t('subscriptionsModule.common', 'Подписка не найдена'));
		}
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel ( $id ) {
		$model = Subscription::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'subscription-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
