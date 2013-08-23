<?php

class DefaultController extends Controller {
	//TODO: set ajaxOnly filter
	public function actionCreate () {
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$state = Yii::app()->getRequest()->getParam('state', 0);

		Yii::import('application.modules.' . strtolower($modelName) . 's.models.*');

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
			$Rating = Rating::model()->findByPk(array(
			                                         'modelName' => $modelName,
			                                         'modelId'   => $modelId
			                                    ));

			Ajax::send(Ajax::AJAX_SUCCESS, 'ok', array(
			                                        'rating' => $Rating->getRating(),
			                                     ));
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, 'not ok', array(
			                                            'errors' => $RatingRelations->getErrors()
			                                       ));
		}
	}
}