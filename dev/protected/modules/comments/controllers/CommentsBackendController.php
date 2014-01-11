<?php

class CommentsBackendController extends YAdminController {

	public function filters () {
		return CMap::mergeArray(parent::filters(), array('postOnly + delete'));
	}

	public function init () {
		parent::init();
	}


	public function actionIndex () {
		$this->breadcrumbs[] = Yii::t('commentsModule.common', 'Управление комментариями');
		$this->pageTitle = Yii::t('commentsModule.common', 'Управление комментариями');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new Comment();
		$model->setScenario('adminSearch');
		$model->unsetAttributes();

		if ( isset($_GET[get_class($model)]) ) {
			$model->setAttributes($_GET[get_class($model)]);
		}

		$criteria = new CDbCriteria();
		$criteria->order = 'ctime DESC';

		$dataProvider = $model->search();
		$dataProvider->getCriteria()->mergeWith($criteria);

		Ajax::renderAjax('index',
			array(
			     'model'        => $model,
			     'dataProvider' => $dataProvider,
			),
			false,
			false,
			true);
	}
	
	
	public function actionCreate () {
		$this->breadcrumbs[] = CHtml::link(Yii::t('commentsModule.common', 'Управление комментариями'), $this->createUrl('/comments/commentsBackend/index'));
		$this->breadcrumbs[] = Yii::t('commentsModule.common', 'Создание комментария');
		$this->pageTitle = Yii::t('commentsModule.common', 'Создание комментария');

		$model = new Comment();

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->saveNode() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('commentsModule.common', 'Комменатрия успешно создан'));
				$this->redirect($this->createUrl('/comments/commentsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('commentsModule.common', 'При сохранении комментария возникли ошибки'));
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
		$this->breadcrumbs[] = CHtml::link(Yii::t('commentsModule.common', 'Управление комментариями'), $this->createUrl('/comments/commentsBackend/index'));
		$this->breadcrumbs[] = Yii::t('commentsModule.common', 'Редактирование комментария');
		$this->pageTitle = Yii::t('commentsModule.common', 'Редактирование комментария');

		$model = Comment::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->saveNode() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('commentsModule.common', 'Комментарий успешно отредактирован'));
				$this->redirect($this->createUrl('/comments/commentsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('commentsModule.common', 'При сохранении комментария возникли ошибки'));
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
		$model = Comment::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( $model->deleteNode() ) {
			Ajax::send(Ajax::AJAX_SUCCESS, 'ok');
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, 'error');
		}
	}
}