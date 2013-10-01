<?php

class DefaultController extends Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + create'));
	}

	public function actionCreate () {
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$state = Yii::app()->getRequest()->getParam('state', 0);

		if ( !class_exists($modelName) ) {
			throw new CHttpException(404);
		}

		$model = $modelName::model()->findByPk($modelId);

		if ( !$model ) {
			throw new CHttpException(404);
		}

		$RatingRelations = new RatingRelations();
		//$Rating = new Rating();

		$RatingRelations->modelName = $modelName;
		$RatingRelations->modelId = $modelId;
		$RatingRelations->rating = ( $state == RatingRelations::RATING_STATE_PLUS ? 1 : -1 );
		$RatingRelations->state = $state;

		if ( $RatingRelations->save() ) {
			/**
			 * запускаем пересчет рейтинга
			 */
			$model->calculateRating();

			Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('ratingsModule.common', 'Рейтинг успешно добавлен'), array(
			                                        'rating' => (int) $model->getRating(),
			                                     ));
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, Yii::t('ratingsModule.common', 'При добавлении рейтинга возникли ошибки'), array(
			                                            'errors' => $RatingRelations->getErrors()
			                                       ));
		}
	}
}