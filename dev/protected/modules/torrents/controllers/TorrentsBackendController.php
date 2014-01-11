<?php
namespace modules\torrents\controllers;

use Yii;
use modules\torrents\models AS models;

class TorrentsBackendController extends \YAdminController {

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
		$this->breadcrumbs[] = Yii::t('torrentsModule.common', 'Управление торрентами');
		$this->pageTitle = Yii::t('torrentsModule.common', 'Управление торрентами');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new models\TorrentGroup();
		$model->setScenario('adminSearch');
		$model->unsetAttributes();

		if ( isset($_GET[$model->resolveClassName($model)]) ) {
			$model->setAttributes($_GET[$model->resolveClassName($model)]);
		}

		$criteria = new \CDbCriteria();
		$criteria->order = 'mtime DESC';
		$model->getDbCriteria()->mergeWith($criteria);

		\Ajax::renderAjax('index',
			array(
				'model' => $model,
			),
			false,
			false,
			true);
	}

	public function actionCreate () {
		$this->breadcrumbs[] = \CHtml::link(Yii::t('torrentsModule.common', 'Управление торрентами'),
			$this->createUrl('/torrents/torrentsBackend/index'));
		$this->breadcrumbs[] = Yii::t('torrentsModule.common', 'Создание торрента');
		$this->pageTitle = Yii::t('torrentsModule.common', 'Создание торрента');

		$model = new models\TorrentGroup();

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage',
					Yii::t('torrentsModule.common', 'Торрент успешно создана'));
				$this->redirect($this->createUrl('/torrents/torrentsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage',
					Yii::t('torrentsModule.common', 'При сохранении торрента возникли ошибки'));
			}
		}

		\Ajax::renderAjax('create',
			array(
				'model' => $model,
			),
			false,
			false,
			true);
	}

	public function actionUpdate ( $id ) {
		$this->breadcrumbs[] = \CHtml::link(Yii::t('torrentsModule.common', 'Управление торрентами'),
			$this->createUrl('/torrents/torrentsBackend/index'));
		$this->breadcrumbs[] = Yii::t('torrentsModule.common', 'Редактирование торрента');
		$this->pageTitle = Yii::t('torrentsModule.common', 'Редактирование торрента');

		$model = models\TorrentGroup::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage',
					Yii::t('torrentsModule.common', 'Торрент успешно создана'));
				$this->redirect($this->createUrl('/torrents/torrentsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage',
					Yii::t('torrentsModule.common', 'При сохранении торрента возникли ошибки'));
			}
		}

		\Ajax::renderAjax('create',
			array(
				'model' => $model,
			),
			false,
			false,
			true);
	}

	public function actionDelete ( $id ) {
		$model = models\TorrentGroup::model()->findByPk($id);
		if ( !$model ) {
			throw new \CHttpException(404);
		}

		if ( $model->delete() ) {
			\Ajax::send(\Ajax::AJAX_SUCCESS, 'ok');
		}
		else {
			\Ajax::send(\Ajax::AJAX_ERROR, 'error');
		}
	}

	public function actionMerge () {
		$ids = Yii::app()->getRequest()->getParam('pk', array());
		if ( !is_array($ids) ) {
			$ids = array($ids);
		}
		$groups = models\TorrentGroup::model()->findAllByPk($ids,
			array('order' => 'FIELD(id, ' . implode(',', $ids) . ')'));
		$first = array_shift($groups);
		$mtime = $first->mtime;

		try {

			$transaction = models\TorrentGroup::model()->getDbConnection()->beginTransaction();

			$firstCommentCount = $first->commentsCount;

			//TODO: убрать в свои модули
			foreach ( $groups AS $group ) {
				$comments = $group->comments;
				$torrents = $group->torrents;
				$ratings = $group->ratings;
				$subscriptions = $group->subscriptions;
				$commentsCount = $group->commentsCount;
				$mtime = max($mtime, $group->mtime);

				foreach ( $comments AS $comment ) {
					$comment->modelId = $first->getId();;
					$comment->saveNode(false);
				}

				foreach ( $torrents AS $torrent ) {
					$first->addTags($torrent->tags->toString());

					$torrent->gId = $first->getId();;
					$torrent->save(false);
				}

				/**
				 * We need to go deeper!
				 */
				try {
					foreach ( $ratings AS $rating ) {
						$rating->modelId = $first->getId();;
						$rating->save(false);
					}

					foreach ( $subscriptions AS $subscription ) {
						$subscription->modelId = $first->getId();;
						$subscription->save(false);
					}
				} catch ( \CException $e ) {

				}

				if ( $firstCommentCount ) {
					$firstCommentCount->count += $commentsCount->count;
					$firstCommentCount->save(false);
				}

				$group->delete();
			}

			$first->mtime = $mtime;
			$first->save(false);
			$transaction->commit();

			\Ajax::send(\Ajax::AJAX_SUCCESS, Yii::t('torrentsModule.common', 'Группы успешно объединены.'));
		} catch ( \CException $e ) {
			$transaction->rollback();
			\Ajax::send(\Ajax::AJAX_WARNING,
				Yii::t('torrentsModule.common', 'При объединении возникли ошибки.'),
				$e->getMessage());
		}
	}
}