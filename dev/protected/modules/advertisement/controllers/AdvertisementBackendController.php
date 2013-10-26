<?php

class AdvertisementBackendController extends YAdminController {

	public $defaultAction = 'index';

	public function init () {
		parent::init();
	}

	public function actionIndex () {
		$this->breadcrumbs[] = Yii::t('advertisementModule.common', 'Управление проблемами');
		$this->pageTitle = Yii::t('advertisementModule.common', 'Управление проблемами');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new Advertisement();
		$model->setScenario('adminSearch');
		$model->unsetAttributes();

		if ( isset($_GET[get_class($model)]) ) {
			$model->setAttributes($_GET[get_class($model)]);
		}

		Ajax::renderAjax('index',
			array(
			     'model'    => $model,
			),
			false,
			false,
			true);
	}
	
	
	public function actionCreate () {
		$this->breadcrumbs[] = CHtml::link(Yii::t('advertisementModule.common', 'Управление рекламой'), $this->createUrl('/advertisement/advertisementBackend/index'));
		$this->breadcrumbs[] = Yii::t('advertisementModule.common', 'Создание рекламного блока');
		$this->pageTitle = Yii::t('advertisementModule.common', 'Создание рекламного блока');

		$model = new Advertisement();

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('advertisementModule.common', 'Рекламный блок успешно создан'));
				$this->redirect($this->createUrl('/advertisement/advertisementBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('advertisementModule.common', 'При сохранении блока возникли ошибки'));
			}
		}

		Ajax::renderAjax('create',
			array(
			     'model'    => $model,
			),
			false,
			false,
			true);
	}

	public function actionUpdate ( $id ) {
		$this->breadcrumbs[] = CHtml::link(Yii::t('advertisementModule.common', 'Управление рекламой'), $this->createUrl('/advertisement/advertisementBackend/index'));
		$this->breadcrumbs[] = Yii::t('advertisementModule.common', 'Редактирование рекламного блока');
		$this->pageTitle = Yii::t('advertisementModule.common', 'Редактирование рекламного блока');

		$model = Advertisement::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('advertisementModule.common', 'Рекламный блок успешно создана'));
				$this->redirect($this->createUrl('/advertisement/advertisementBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('advertisementModule.common', 'При сохранении блока возникли ошибки'));
			}
		}

		Ajax::renderAjax('create',
			array(
			     'model'    => $model,
			),
			false,
			false,
			true);
	}

	public function actionDelete ( $id ) {
		$model = Advertisement::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( $model->delete() ) {
			Ajax::send(Ajax::AJAX_SUCCESS, 'ok');
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, 'error');
		}
	}

	public function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'category-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}