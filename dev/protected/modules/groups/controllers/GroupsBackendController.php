<?php

class GroupsBackendController extends YAdminController {

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
		$this->breadcrumbs[] = Yii::t('GroupsModule.common', 'Управление группами');
		$this->pageTitle = Yii::t('GroupsModule.common', 'Управление группами');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new Group();
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
		$this->breadcrumbs[] = CHtml::link(Yii::t('GroupsModule.common', 'Управление группами'), $this->createUrl('/groups/groupsBackend/index'));
		$this->breadcrumbs[] = Yii::t('GroupsModule.common', 'Создание группы');
		$this->pageTitle = Yii::t('GroupsModule.common', 'Создание группы');

		$model = new Group();

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('GroupsModule.common', 'Группа успешно создана'));
				$this->redirect($this->createUrl('/groups/groupsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('GroupsModule.common', 'При сохранении группы возникли ошибки'));
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
		$this->breadcrumbs[] = CHtml::link(Yii::t('GroupsModule.common', 'Управление группами'), $this->createUrl('/groups/groupsBackend/index'));
		$this->breadcrumbs[] = Yii::t('GroupsModule.common', 'Редактирование группы');
		$this->pageTitle = Yii::t('GroupsModule.common', 'Редактирование группы');

		$model = Group::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('GroupsModule.common', 'Группа успешно создана'));
				$this->redirect($this->createUrl('/groups/groupsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('GroupsModule.common', 'При сохранении группы возникли ошибки'));
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
		$model = Group::model()->findByPk($id);
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