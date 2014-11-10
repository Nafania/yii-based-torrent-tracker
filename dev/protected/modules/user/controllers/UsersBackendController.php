<?php

class UsersBackendController extends YAdminController {

	public function filters () {
		return parent::filters();
	}

	public function init () {
		parent::init();
	}

	public function actions () {
		return array(
			'toggle' => array(
				'class' => 'application.modules.yiiadmin.actions.ActionToggle'
			)
		);
	}

	public function actionIndex () {
		$this->breadcrumbs[] = Yii::t('userModule.common', 'Управление пользователями');
		$this->pageTitle = Yii::t('userModule.common', 'Управление пользователями');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new User();
		$model->setScenario('adminSearch');
		$model->unsetAttributes();

		if ( isset($_GET[get_class($model)]) ) {
			$model->setAttributes($_GET[get_class($model)]);
		}

		Ajax::renderAjax('index',
			array(
				'model' => $model,
			),
			false,
			false,
			true);
	}

	public function actionToggleBan () {
		$pk = Yii::app()->getRequest()->getPost('pk', 0);

		$model = User::model()->findByPk($pk);

		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( $model->active ) {
			$model->ban();
		}
		else {
			$model->unBan();
		}

		list($images, $titles) = Yii::app()->getModule('yiiadmin')->getToggleImages($model, 'active');

		Ajax::send(Ajax::AJAX_SUCCESS,
			YiiadminModule::t('Запись сохранена'),
			array(
				'image'      => $images[$model->active],
				'imageTitle' => $titles[$model->active],
			));
	}
}