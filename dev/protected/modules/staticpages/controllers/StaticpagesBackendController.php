<?php

class StaticpagesBackendController extends YAdminController {

	public function filters () {
		return parent::filters();
	}

	public function init () {
		parent::init();
	}

	public function actionIndex () {
		$this->breadcrumbs[] = Yii::t('staticpagesModule.common', 'Управление статичными страницами');
		$this->pageTitle = Yii::t('staticpagesModule.common', 'Управление статичными страницами');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new StaticPage();

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
		$this->breadcrumbs[] = CHtml::link(Yii::t('staticpagesModule.common', 'Управление статичными страницами'), $this->createUrl('/staticpages/staticpagesBackend/index'));
		$this->breadcrumbs[] = Yii::t('staticpagesModule.common', 'Создание страницы');
		$this->pageTitle = Yii::t('staticpagesModule.common', 'Создание страницы');

		$model = new StaticPage();

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('staticpagesModule.common', 'Страница успешно создана'));
				$this->redirect($this->createUrl('/staticpages/staticpagesBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('staticpagesModule.common', 'При сохранении страницы возникли ошибки'));
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
		$this->breadcrumbs[] = CHtml::link(Yii::t('staticpagesModule.common', 'Управление статичными страницами'), $this->createUrl('/staticpages/staticpagesBackend/index'));
		$this->breadcrumbs[] = Yii::t('staticpagesModule.common', 'Редактирование страницы');
		$this->pageTitle = Yii::t('staticpagesModule.common', 'Редактирование страницы');

		$model = StaticPage::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('staticpagesModule.common', 'Страница успешно создана'));
				$this->redirect($this->createUrl('/staticpages/staticpagesBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('staticpagesModule.common', 'При сохранении страницы возникли ошибки'));
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
		$model = StaticPage::model()->findByPk($id);
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
}