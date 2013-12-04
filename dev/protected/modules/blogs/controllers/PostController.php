<?php
namespace modules\blogs\controllers;

use Yii;
use CMap;
use CHttpException;
use components;
use modules\blogs\models AS models;

class PostController extends components\Controller {

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
		$blog = models\Blog::model()->findByPk($blogId);

		if ( !$blog ) {
			throw new CHttpException(404);
		}

		if ( !Yii::app()->user->checkAccess('createPostInOwnBlog',
				array('ownerId' => $blog->ownerId)) && !Yii::app()->user->checkAccess('createPostInBlog') && !Yii::app()->user->checkAccess('createPostInGroupMemberBlog',
				array('isMember' => \Group::checkJoin($blog->group))) && !Yii::app()->user->checkAccess('createPostInGroup')
		) {
			throw new CHttpException(403);
		}

		if ( $blog->groupId ) {
			$this->pageTitle = Yii::t('blogsModule.common',
				'Создание поста в группе "{groupTitle}"',
				array(
				     '{groupTitle}' => $blog->group->getTitle(),
				));
			$this->breadcrumbs = array(
				Yii::t('groupsModule.common', 'Просмотр групп')                => array('/groups/default/index'),
				Yii::t('groupsModule.common',
					'Просмотр группы "{groupTitle}"',
					array('{groupTitle}' => $blog->group->getTitle())) => $blog->group->getUrl(),
				Yii::t('groupsModule.common',
					'Создание поста в группе "{groupTitle}"',
					array(
					     '{groupTitle}' => $blog->group->getTitle(),
					))
			);
		}
		else {
			$this->pageTitle = Yii::t('blogsModule.common',
				'Создание поста в блоге "{blogTitle}"',
				array(
				     '{blogTitle}' => $blog->getTitle(),
				));
			$this->breadcrumbs = array(
				Yii::t('blogsModule.common', 'Блоги')             => array('/blogs/default/index'),
				Yii::t('blogsModule.common',
					'Просмотр блога "{blogPostName}"',
					array('{blogPostName}' => $blog->getTitle())) => $blog->getUrl(),
				Yii::t('blogsModule.common',
					'Создание поста в блоге "{blogTitle}"',
					array(
					     '{blogTitle}' => $blog->getTitle(),
					))
			);
		}

		$blogPost = new models\BlogPost();

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($blogPost);

		if ( isset($_POST[$blogPost->resolveClassName()]) ) {
			$blogPost->attributes = $_POST[$blogPost->resolveClassName()];
			$blogPost->blogId = $blogId;
			$blogPost->setTags($_POST['blogTags']);

			if ( $blogPost->save() ) {

				Yii::app()->user->setFlash(\User::FLASH_SUCCESS,
					Yii::t('blogsModule.common', 'Пост создан успешно'));
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


		if ( $blog->groupId ) {
			$this->pageTitle = Yii::t('blogsModule.common',
				'Редактирование поста в группе "{groupTitle}"',
				array(
				     '{groupTitle}' => $blog->group->getTitle(),
				));
			$this->breadcrumbs = array(
				Yii::t('groupsModule.common', 'Просмотр групп')                => array('/groups/default/index'),
				Yii::t('groupsModule.common',
					'Просмотр группы "{groupTitle}"',
					array('{groupTitle}' => $blog->group->getTitle())) => $blog->group->getUrl(),
				Yii::t('groupsModule.common',
					'Редактирование поста в группе "{groupTitle}"',
					array(
					     '{groupTitle}' => $blog->group->getTitle(),
					))
			);
		}
		else {
			$this->pageTitle = Yii::t('blogsModule.common',
				'Редактирование поста в блоге "{blogTitle}"',
				array(
				     '{blogTitle}' => $blog->getTitle(),
				));
			$this->breadcrumbs = array(
				Yii::t('blogsModule.common', 'Блоги')             => array('/blogs/default/index'),
				Yii::t('blogsModule.common',
					'Просмотр блога "{blogPostName}"',
					array('{blogPostName}' => $blog->getTitle())) => $blog->getUrl(),
				Yii::t('blogsModule.common',
					'Редактирование поста в блоге "{blogTitle}"',
					array(
					     '{blogTitle}' => $blog->getTitle(),
					))
			);
		}

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($blogPost);

		if ( isset($_POST[$blogPost->resolveClassName()]) ) {
			$blogPost->attributes = $_POST[$blogPost->resolveClassName()];
			$blogPost->setTags($_POST['blogTags']);

			if ( $blogPost->save() ) {

				Yii::app()->user->setFlash(\User::FLASH_SUCCESS,
					Yii::t('blogsModule.common', 'Пост отредактирован успешно'));
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

		if ( $blog->groupId ) {
			$this->breadcrumbs[Yii::t('groupsModule.common', 'Просмотр групп')] = array('/groups/default/index');
			$this->breadcrumbs[Yii::t('groupsModule.common',
				'Просмотр группы "{groupName}"',
				array('{groupName}' => $blog->group->getTitle()))] = $blog->group->getUrl();
		}
		else {
			$this->breadcrumbs[Yii::t('blogsModule.common', 'Блоги')] = array('/blogs/default/index');
			$this->breadcrumbs[Yii::t('blogsModule.common',
				'Просмотр блога "{blogPostName}"',
				array('{blogPostName}' => $blog->getTitle()))] = $blog->getUrl();
		}

		$this->breadcrumbs[] = $this->pageTitle = Yii::t('blogsModule.common',
			'Просмотр поста "{blogTitle}"',
			array(
			     '{blogTitle}' => $blogPost->getTitle(),
			));

		$this->render('view',
			array(
			     'blogPost' => $blogPost
			));
	}


	public function actionTagsSuggest ( $q ) {
		$criteria = new \CDbCriteria();
		$criteria->addSearchCondition('t.name', $q, true);
		$criteria->group = 't.name';
		$tags = models\BlogPost::model()->with('blog:forCurrentUser')->getAllTags($criteria);

		$result = array();

		foreach ( $tags AS $tag ) {
			$result[] = array(
				'id'   => $tag,
				'text' => $tag
			);
		}

		\Ajax::send(\Ajax::AJAX_SUCCESS,
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
			Yii::app()->user->setFlash(\User::FLASH_SUCCESS,
				Yii::t('blogsModule.common', 'Пост удален успешно'));
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/blogs/default/index'));
		}
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer $id the ID of the model to be loaded
	 *
	 * @return models\BlogPost the loaded model
	 * @throws CHttpException
	 */
	public function loadModel ( $id ) {
		$model = models\BlogPost::model()->findByPk($id);
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
			echo \CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


	public function getPagesForSitemap () {
		return models\BlogPost::model()->onlyVisible();
	}
}