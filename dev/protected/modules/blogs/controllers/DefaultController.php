<?php
namespace modules\blogs\controllers;
use Yii;
use components;
use modules\blogs\models AS models;

class DefaultController extends components\Controller {
	/**
	 * @return array action filters
	 */
	public function filters () {
		//return CMap::mergeArray(parent::filters(),
		return array(
			'postOnly + delete',
			'ajaxOnly + tagsSuggest',
			array('application.modules.auth.filters.AuthFilter - tagsSuggest'),
			// we only allow deletion via POST request
		);
		//));
	}

	/**
	 * Displays a particular model.
	 *
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView ( $id ) {
		$model = $this->loadModel($id);

		$this->pageTitle = Yii::t('blogsModule.common',
			'Просмотр блога "{blogPostName}"',
			array('{blogPostName}' => $model->getTitle()));
		$this->breadcrumbs = array(
			Yii::t('blogsModule.common', 'Блоги') => array('index'),
			Yii::t('blogsModule.common',
						'Просмотр блога "{blogPostName}"',
						array('{blogPostName}' => $model->getTitle()))
		);
		$blogPost = new models\BlogPost('search');
		$blogPost->forBlog($id);

		/**
		 * Если текущий юзер не владелец блога, то показываем только не скрытые посты
		 */
		if ( Yii::app()->getUser()->getId() != $model->ownerId ) {
			$blogPost->onlyVisible();
		}

		$blogPost->unsetAttributes(); // clear any default values
		//$blogPost->setScenario('search');

		$attributes = Yii::app()->getRequest()->getQuery('BlogPost', '');
		$search = Yii::app()->getRequest()->getParam('search', '');
		$tags = Yii::app()->getRequest()->getParam('tags', '');

		//$model->title = $search;
		$blogPost->attributes = $attributes;
		$blogPost->searchWithText($search);
		$blogPost->searchWithTags($tags);

		$postsProvider = $blogPost->search();

		$this->render('view',
			array(
			     'model' => $model,
			     'postsProvider' => $postsProvider
			));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate () {
		$this->pageTitle = Yii::t('blogsModule.common', 'Создание блога');
		$this->breadcrumbs[] = Yii::t('blogsModule.common', 'Создание блога');

		$blog = new models\Blog();

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($blog);

		if ( isset($_POST[$blog->resolveClassName()]) ) {
			$blog->attributes = $_POST[$blog->resolveClassName()];

			if ( $blog->save() ) {

				Yii::app()->user->setFlash(\User::FLASH_SUCCESS,
					Yii::t('blogsModule.common', 'Блог создан успешно'));
				$this->redirect($blog->getUrl());
			}
		}

		$this->render('create',
			array(
			     'blog' => $blog,
			));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate ( $id ) {
		$this->pageTitle = Yii::t('blogsModule.common', 'Редактирование блога');
		$this->breadcrumbs[] = Yii::t('blogsModule.common', 'Редактирование блога');

		$blog = $this->loadModel($id);

		if ( !Yii::app()->user->checkAccess('updateOwnBlog',
			array('ownerId' => $blog->ownerId)) && !Yii::app()->user->checkAccess('updateBlog')
		) {
			throw new \CHttpException(403);
		}

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($blog);


		if ( isset($_POST[$blog->resolveClassName()]) ) {
			$blog->attributes = $_POST[$blog->resolveClassName()];

			if ( $blog->save() ) {

				Yii::app()->user->setFlash(\User::FLASH_SUCCESS,
					Yii::t('blogsModule.common', 'Блог отредактирован успешно'));
				$this->redirect(array('my'));

			}

		}

		$this->render('update',
			array(
			     'blog' => $blog,
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
			Yii::app()->user->setFlash(\User::FLASH_SUCCESS,
				Yii::t('blogsModule.common', 'Блог удален успешно'));
			$this->redirect(Yii::app()->getUser()->returnUrl);
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex () {
		$this->pageTitle = Yii::t('blogsModule.common', 'Блоги');
		$this->breadcrumbs[] = Yii::t('blogsModule.common', 'Блоги');

		$model = models\Blog::model()->onlyUsers();
		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$attributes = Yii::app()->getRequest()->getQuery('BlogPost', '');

		$model->attributes = $attributes;

		$dataProvider = $model->search();

		\Ajax::renderAjax('index',
			array(
			     'dataProvider' => $dataProvider,
			     'model'        => $model,
			),
			false,
			false,
			true);
	}

	public function actionMy () {
		$this->pageTitle = Yii::t('blogsModule.common', 'Мои блоги');
		$this->breadcrumbs[] = Yii::t('blogsModule.common', 'Мои блоги');

		$model = models\Blog::model()->forCurrentUser();
		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$dataProvider = $model->search();

		\Ajax::renderAjax('my',
			array(
			     'dataProvider' => $dataProvider,
			     'model'        => $model,
			),
			false,
			false,
			true);
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer $id the ID of the model to be loaded
	 *
	 * @return models\BlogPost the loaded model
	 * @throws \CHttpException
	 */
	public function loadModel ( $id ) {
		$model = models\Blog::model()->findByPk($id);
		if ( $model === null ) {
			throw new \CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param $model array the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( !is_array($model) ) {
			$model = array($model);
		}
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'blog-form' ) {
			echo \CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function getPagesForSitemap () {
		return models\Blog::model()->onlyUsers();
	}
}
