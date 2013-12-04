<?php

class DefaultController extends components\Controller {

	public function actionCreate () {
		$formId = Yii::app()->getRequest()->getParam('formId', '');
		$data = Yii::app()->getRequest()->getParam('data', array());

		$draft = Draft::model()->findByPk($formId);
		if ( !$draft ) {
			$draft = new Draft();
		}

		$draft->formId = $formId;
		$draft->data = serialize(CJSON::decode($data));

		if ( $draft->save() ) {
			Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('draftsModule.common', 'Черновик успешно сохранен.'));
		}
		else {
			Ajax::send(Ajax::AJAX_WARNING,
				Yii::t('draftsModule.common', 'При сохранении черновика произошли ошибки.'),
				$draft->getErrors());
		}
	}

	public function actionGet () {
		$formId = Yii::app()->getRequest()->getParam('formId', '');

		$draft = $this->loadModel($formId);

		Ajax::send(Ajax::AJAX_SUCCESS,
			'ok',
			array(
			     'data'    => unserialize($draft->data),
			     'mtime'   => (int) $draft->mtime,
			     'deleted' => (int) $draft->deleted,
			));
	}

	public function actionDelete () {
		$formId = Yii::app()->getRequest()->getParam('formId', '');

		$draft = $this->loadModel($formId);

		if ( !Yii::app()->getUser()->checkAccess('deleteOwnDraft',
			array(
			     'uId' => $draft->uId
			))
		) {
			throw new CHttpException(403);
		}

		if ( $draft->delete() ) {
			Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('draftsModule.common', 'Черновик успешно удален.'));
		}
		else {
			Ajax::send(Ajax::AJAX_WARNING, Yii::t('draftsModule.common', 'При удалении черновика произошли ошибки.'));
		}
	}

	protected function loadModel ( $pk ) {
		$model = Draft::model()->findByPk($pk);

		if ( !$model ) {
			throw new CHttpException(404, Yii::t('draftsModule.common', 'Черновик не найден.'));
		}

		return $model;
	}
}