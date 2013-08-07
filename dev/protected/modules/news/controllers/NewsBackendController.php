<?php

class NewsBackendController extends YAdminController {

	public function filters () {
		return CMap::mergeArray(parent::filters(), array('postOnly + delete'), array('ajaxOnly + pin'));
	}

	public function init () {
		parent::init();
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate () {
		$this->breadcrumbs[] = CHtml::link(Yii::t('newsModule.common', 'Управление новостями'), $this->createUrl('/news/newsBackend/index'));
		$this->breadcrumbs[] = Yii::t('newsModule.common', 'Создание новости');
		$this->pageTitle = Yii::t('newsModule.common', 'Создание новости');

		$model = new News();
		$this->performAjaxValidation($model);

		if ( isset($_POST['News']) ) {
			$model->attributes = $_POST['News'];
			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('newsModule.common', 'Новость успешно создана'));

				$this->redirect(array('index'));
			}
		}

		$this->render('create',
			array(
			     'model' => $model,
			));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate ( $id ) {
		$this->breadcrumbs[] = CHtml::link(Yii::t('newsModule.common', 'Управление новостями'), $this->createUrl('/news/newsBackend/index'));
		$this->breadcrumbs[] = Yii::t('newsModule.common', 'Редактирование новости');
		$this->pageTitle = Yii::t('newsModule.common', 'Редактирование новости');

		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset($_POST['News']) ) {
			$model->attributes = $_POST['News'];
			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('newsModule.common', 'Новость успешно изменена'));

				$this->redirect(array('index'));
			}
		}

		$this->render('update',
			array(
			     'model' => $model,
			));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete ( $id ) {
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if ( !isset($_GET['ajax']) ) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex () {
		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$this->breadcrumbs[] = Yii::t('newsModule.common', 'Управление новостями');
		$this->pageTitle = Yii::t('newsModule.common', 'Управление новостями');

		$model = new News('search');
		$model->unsetAttributes();

		if ( isset($_GET['News']) ) {
			$model->attributes = $_GET['News'];
		}

		Ajax::renderAjax('index', array(
		                               'model' => $model,
		                          ));
	}


	public function actionPin () {
		$pk = Yii::app()->getRequest()->getParam('pk', array());
		$val = Yii::app()->getRequest()->getParam('val', '');

		$News = News::model()->findAllByPk($pk);
		if ( !$News ) {
			throw new CHttpException(404, 'News not found');
		}

		$errors = array();
		foreach ( $News AS $_News ) {
			if ( $val !== '' ) {
				$_News->pinned = $val;
			}
			else {
				$_News->pinned = (int) !$_News->pinned;
			}
			if ( !$_News->save() ) {
				$errors[] = implode(', ', $_News->getErrors());
			}
		}
		list($images, $titles) = Yii::app()->getModule('yiiadmin')->getToggleImages($_News, 'pinned');


		if ( $errors ) {
			Ajax::send(Ajax::AJAX_ERROR,
				YiiadminModule::t('При сохранении возникли ошибки: {errors}',
					array('{errors}' => implode(', ', $errors))),
				array(
				     'image'      => $images[!$_News->pinned],
				     'imageTitle' => $titles[!$_News->pinned],
				));
		}
		else {
			Ajax::send(Ajax::AJAX_SUCCESS,
				YiiadminModule::t('Запись сохранена'),
				array(
				     'image'      => $images[$_News->pinned],
				     'imageTitle' => $titles[$_News->pinned],
				));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer $id the ID of the model to be loaded
	 *
	 * @return News the loaded model
	 * @throws CHttpException
	 */
	public function loadModel ( $id ) {
		$model = News::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param News $model the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'news-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
