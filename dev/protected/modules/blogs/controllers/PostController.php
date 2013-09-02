<?php

class PostController extends Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
		return CMap::mergeArray(parent::filters(),
			array(
			     'postOnly + delete',
			));
	}

	public function actionCreate ( $blogId ) {
		$blog = Blog::model()->findByPk($blogId);

		if ( !$blog ) {
			throw new CHttpException(404);
		}

		if ( !Yii::app()->user->checkAccess('createPostInOwnBlog',
			array('ownerId' => $blog->ownerId)) && !Yii::app()->user->checkAccess('createPostInBlog')
		) {
			throw new CHttpException(403);
		}


		$this->pageTitle = Yii::t('blogsModule.common',
			'Создание записи в блоге "{blogTitle}"',
			array(
			     '{blogTitle}' => $blog->getTitle(),
			));
		$this->breadcrumbs = array(
			Yii::t('blogsModule.common', 'Блоги')             => array('/blogs/default/index'),
			Yii::t('blogsModule.common',
				'Просмотр блога "{blogPostName}"',
				array('{blogPostName}' => $blog->getTitle())) => $blog->getUrl(),
			Yii::t('blogsModule.common',
				'Создание записи в блоге "{blogTitle}"',
				array(
				     '{blogTitle}' => $blog->getTitle(),
				))
		);

		$blogPost = new BlogPost();

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($blog);

		if ( isset($_POST['BlogPost']) ) {
			$blogPost->attributes = $_POST['BlogPost'];
			$blogPost->blogId = $blogId;
			$blogPost->setTags($_POST['tags']);

			if ( $blogPost->save() ) {

				Yii::app()->user->setFlash(User::FLASH_SUCCESS,
					Yii::t('blogsModule.common', 'Запись создана успешно'));
				$this->redirect($blogPost->getUrl());
			}
		}

		$this->render('create',
			array(
			     'blogPost' => $blogPost,
			     'blog'     => $blog,
			));
	}


	public function actionUpdate ( $id ) {
		$blogPost = $this->loadModel($id);
		$blog = $blogPost->blog;

		if ( !Yii::app()->user->checkAccess('updatePostInOwnBlog',
			array('ownerId' => $blog->ownerId)) && !Yii::app()->user->checkAccess('updatePostInBlog')
		) {
			throw new CHttpException(403);
		}


		$this->pageTitle = Yii::t('blogsModule.common',
			'Создание записи в блоге "{blogTitle}"',
			array(
			     '{blogTitle}' => $blog->getTitle(),
			));
		$this->breadcrumbs[Yii::t('blogsModule.common', 'Блоги')] = array('/blogs/default/index');
		$this->breadcrumbs[Yii::t('blogsModule.common',
			'Просмотр блога "{blogPostName}"',
			array('{blogPostName}' => $blog->getTitle()))] = $blog->getUrl();
		$this->breadcrumbs[] = Yii::t('blogsModule.common',
			'Редактирование записи в блоге "{blogTitle}"',
			array(
			     '{blogTitle}' => $blog->getTitle(),
			));

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($blog);

		if ( isset($_POST['BlogPost']) ) {
			$blogPost->attributes = $_POST['BlogPost'];
			$blogPost->setTags($_POST['tags']);

			if ( $blogPost->save() ) {

				Yii::app()->user->setFlash(User::FLASH_SUCCESS,
					Yii::t('blogsModule.common', 'Запись отредатктирована успешно'));
				$this->redirect($blogPost->getUrl());
			}
		}

		$this->render('update',
			array(
			     'blogPost' => $blogPost,
			     'blog'     => $blog,
			));
	}


	public function actionView ( $id ) {
		$blogPost = $this->loadModel($id);
		$blog = $blogPost->blog;

		$this->pageTitle = Yii::t('blogsModule.common',
			'Просмотр записи "{blogTitle}"',
			array(
			     '{blogTitle}' => $blogPost->getTitle(),
			));
		$this->breadcrumbs[Yii::t('blogsModule.common', 'Блоги')] = array('/blogs/default/index');
		$this->breadcrumbs[Yii::t('blogsModule.common',
			'Просмотр блога "{blogPostName}"',
			array('{blogPostName}' => $blog->getTitle()))] = $blog->getUrl();
		$this->breadcrumbs[] = Yii::t('blogsModule.common',
			'Просмотр записи "{blogTitle}"',
			array(
			     '{blogTitle}' => $blogPost->getTitle(),
			));
		$this->render('view',
			array(
			     'blogPost' => $blogPost
			));
	}


	public function actionTagsSuggest ( $q ) {
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('t.name', $q, true);
		$criteria->group = 't.name';
		$tags = BlogPost::model()->with('blog:forCurrentUser')->getAllTags($criteria);

		$result = array();

		foreach ( $tags AS $tag ) {
			$result[] = array(
				'id'   => $tag,
				'text' => $tag
			);
		}

		Ajax::send(Ajax::AJAX_SUCCESS,
			'ok',
			array(
			     'tags'  => $result,
			     'total' => sizeof($result)
			));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete ( $id ) {
		$model = $this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if ( !isset($_GET['ajax']) ) {
			Yii::app()->user->setFlash(User::FLASH_SUCCESS,
				Yii::t('blogsModule.common', 'Запись удалена успешно'));
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/blogs/default/index'));
		}
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer $id the ID of the model to be loaded
	 *
	 * @return BlogPost the loaded model
	 * @throws CHttpException
	 */
	public function loadModel ( $id ) {
		$model = BlogPost::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
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
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'blogPost-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}