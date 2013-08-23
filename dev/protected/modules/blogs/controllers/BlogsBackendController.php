<?php

class BlogsBackendController extends YAdminController {

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
		$this->breadcrumbs[] = Yii::t('blogsModule.common', 'Управление постами');
		$this->pageTitle = Yii::t('blogsModule.common', 'Управление постами');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new BlogPost();
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
		$this->breadcrumbs[] = CHtml::link(Yii::t('blogsModule.common', 'Управление постами'), $this->createUrl('/blogs/blogsBackend/index'));
		$this->breadcrumbs[] = Yii::t('blogsModule.common', 'Создание поста');
		$this->pageTitle = Yii::t('blogsModule.common', 'Создание поста');

		$model = new BlogPost();

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('blogsModule.common', 'Пост успешно создана'));
				$this->redirect($this->createUrl('/blogs/blogsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('blogsModule.common', 'При сохранении поста возникли ошибки'));
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
		$this->breadcrumbs[] = CHtml::link(Yii::t('blogsModule.common', 'Управление постами'), $this->createUrl('/blogs/blogsBackend/index'));
		$this->breadcrumbs[] = Yii::t('blogsModule.common', 'Редактирование поста');
		$this->pageTitle = Yii::t('blogsModule.common', 'Редактирование поста');

		$model = BlogPost::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('blogsModule.common', 'Пост успешно создана'));
				$this->redirect($this->createUrl('/blogs/blogsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('blogsModule.common', 'При сохранении поста возникли ошибки'));
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
		$model = BlogPost::model()->findByPk($id);
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