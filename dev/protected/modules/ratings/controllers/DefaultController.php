<?php

class DefaultController extends components\Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + create'));
	}

	public function actionCreate () {
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$state = Yii::app()->getRequest()->getParam('state', 0);

		$className = EActiveRecord::classNameToNamespace($modelName);

		if ( !class_exists($className) ) {
			throw new CHttpException(404);
		}

		$model = $className::model()->findByPk($modelId);

		if ( !$model ) {
			throw new CHttpException(404);
		}


		if ( !($errors = $model->addRating($state)) ) {
			Ajax::send(Ajax::AJAX_SUCCESS,
				Yii::t('ratingsModule.common', 'Рейтинг успешно добавлен'),
				array(
				     'rating' => (int) $model->getRating(),
				));
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR,
				Yii::t('ratingsModule.common', 'При добавлении рейтинга возникли ошибки'),
				array(
				     'errors' => $errors
				));
		}
	}
}