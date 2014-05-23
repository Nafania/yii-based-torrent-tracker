<?php

class EventController extends components\Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
		return CMap::mergeArray(parent::filters(),
			array(
				'ajaxOnly + getList, read',
			));
	}

	public function actionRead () {
		$id = Yii::app()->getRequest()->getParam('id', 0);

		if ( $id == 'all' ) {
			$events = Event::model()->unreaded()->forCurrentUser()->findAll();
			foreach ( $events AS $event ) {
				$event->unread = Event::EVENT_READED;
				$event->save();
			}
		}
		else {
			$event = $this->loadModel($id);
			$event->unread = Event::EVENT_READED;
			$event->save();
		}
	}

	public function actionGetList () {
        $eventItemsCount = (int) Yii::app()->redis->hGet(Event::REDIS_HASH_NAME, \Yii::app()->getUser()->getId());

        if ( $eventItemsCount ) {
		    $events = Event::model()->unreaded()->forCurrentUser()->findAll();
        }
        else {
            $events = [];
        }

		$view = $this->renderPartial('list',
			array(
				'events' => $events
			),
			true,
			false);

		Ajax::send(Ajax::AJAX_SUCCESS,
			'ok',
			array(
				'view' => $view
			));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel ( $id ) {
		$model = Event::model()->forCurrentUser()->findByPk($id);
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
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'event-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
