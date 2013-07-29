<?php

class CategoryBackendController extends YAdminController {

	public $defaultAction = 'create';

	public function filters () {
		return CMap::mergeArray(array(
		                             'ajaxOnly + move, loadTree',
		                             'postOnly + delete',
		                        ),
			parent::filters());
	}

	public function init () {
		parent::init();

		$this->breadcrumbs[] = CHtml::link(Yii::t('CategoryModule', 'Управление категориями'),
			Yii::app()->createUrl('category/categoryBackend/'));
	}

	public function actionLoadTree () {
		$tree = Category::model()->getTree();

		echo CJSON::encode($tree);
		Yii::app()->end();
	}

	public function actionCreate () {
		$this->breadcrumbs[] = Yii::t('CategoryModule', 'Создание категории');
		$this->pageTitle = Yii::t('CategoryModule', 'Создание категории');

		$criteria = new CDbCriteria;
		$criteria->order = 't.root, t.lft'; // or 't.root, t.lft' for multiple trees
		$categories = Category::model()->findAll($criteria);

		$Category = new Category('create');

		$this->performAjaxValidation($Category);

		if ( isset($_POST['Category']) ) {
			$Category->attributes = $_POST['Category'];
			//$Category->image = CUploadedFile::getInstance($Category, 'image');

			if ( $Category->saveNode() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('CategoryModule', 'Категория добавлена успешно'));
			}

		}

		$this->render('index',
			array(
			     'model'      => $Category,
			     'categories' => $categories,
			     //'model'     => new Category('create'),
			));
	}

	public function actionUpdate ( $id ) {
		$this->breadcrumbs[] = Yii::t('CategoryModule', 'Редактирование категории');
		$this->pageTitle = Yii::t('CategoryModule', 'Редактирование категории');

		$Category = Category::model()->findByPk($id);
		if ( $Category === null ) {
			throw new CException(Yii::t('CategoryModule', 'Категория не найдена'));
		}

		$this->performAjaxValidation($Category);

		if ( isset($_POST['Category']) ) {
			$Category->attributes = $_POST['Category'];
			//$Category->image = CUploadedFile::getInstance($Category, 'image');

			if ( $Category->saveNode() ) {

				if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
					Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('CategoryModule', 'Категория добавлена успешно'));
				}
				else {
					Yii::app()->user->setFlash('flashMessage', YiiadminModule::t('Запись сохранена успешно'));
					$this->redirect(Yii::app()->createUrl('category/categoryBackend/'));
				}
			}
			else {
				if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
					Ajax::send(Ajax::AJAX_ERROR,
						Yii::t('CategoryModule', 'Возникли ошибки при добавлении категории: {errors}', array('{errors}' => implode(', ', array_values($Category->getErrors())))));
				}
				else {

				}

			}
		}
		Ajax::renderAjax('update',
			array(
			     'model' => $Category,

			),
			false,
			true);
	}

	public function actionDelete () {
		$this->breadcrumbs[] = Yii::t('CategoryModule', 'Удаление категории');
		$this->pageTitle = Yii::t('CategoryModule', 'Удаление категории');

		$id = Yii::app()->getRequest()->getParam('source');

		$Category = Category::model()->findByPk($id);

		if ( !$Category ) {
			throw new CHttpException(404);
		}

		$prevnode = $Category->next()->find();
		if ( !$prevnode ) {
			$prevnode = $Category->prev()->find();
		}
		if ( !$prevnode ) {
			$prevnode = $Category->parent()->find();
		}

		if ( $Category->deleteNode() ) {
			Ajax::send(Ajax::AJAX_SUCCESS,
				Yii::t('CategoryModule',
					'Категория удалена успешно'),
				array(
				     'node'        => $Category->id,
				     'key'         => $prevnode->id,
				     'redirectUrl' => Yii::app()->createUrl('category/categoryBackend')
				));
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR,
				Yii::t('CategoryModule', 'Возникли ошибки при удалении категории'));
		}
	}

	public function actionMove () {
		$source = Yii::app()->getRequest()->getPost('source', 0);
		$target = Yii::app()->getRequest()->getPost('target', 0);
		$mode = Yii::app()->getRequest()->getPost('mode', '');

		$node = Category::model()->findByPk($source);
		$target = Category::model()->findByPk($target);

		try {
			switch ( $mode ) {
				case 'before':
					if ( $target->isRoot() ) {
						$status = $node->moveAsRoot();
					}
					else {
						$status = $node->moveBefore($target);
					}
					break;
				case 'after':
					if ( $target->isRoot() ) {
						$status = $node->moveAsRoot();
					}
					else {
						$status = $node->moveAfter($target);
					}
					break;
				case 'over':
					$status = $node->moveAsLast($target);
					break;
			}
			$return = array(
				'sourceNode' => $node->primaryKey,
				'node'       => $target->primaryKey,
				'mode'       => $mode
			);
			if ( $status ) {
				Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('CategoryModule', 'Категория удалена успешно'), $return);
			}
			else {
				Ajax::send(Ajax::AJAX_ERROR, Yii::t('CategoryModule', 'Категория удалена успешно'), $return);
			}
		} catch ( Exception $e ) {
			Ajax::send(Ajax::AJAX_ERROR, $e->getMessage());
		}
	}

	public function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'category-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}