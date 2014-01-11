<?php
namespace modules\torrents\controllers;

use Yii;
use CMap;
use CDbCriteria;
use CException;
use CHttpException;
use CArrayDataProvider;
use CJSON;
use CHtmlPurifier;
use components;
use modules\torrents\models AS models;
use modules\torrents\components AS tComponents;

class DefaultController extends components\Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
		return CMap::mergeArray(parent::filters(),
			array(
				'postOnly + delete, deleteTorrent',
				'ajaxOnly + fileList, tagsSuggest, suggest',
			));
	}


	public function beforeAction ( $action ) {
		parent::beforeAction($action);

		$title = Yii::t('torrentsModule.common', 'Торренты');

		if ( $action->getId() == 'index' ) {
			$this->breadcrumbs[] = $title;
		}
		else {
			$this->breadcrumbs[$title] = array('index');
		}

		return true;
	}

	/**
	 * Displays a particular model.
	 *
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView ( $id ) {
		$model = $this->loadModel($id)->deleteForRussianOrError();

		$title = Yii::t('torrentsModule.common', '{torrentName}', array('{torrentName}' => $model->getTitle()));
		$this->breadcrumbs[] = $title;

		$this->pageTitle = $title;
		$this->pageDescription = $model->getDescription();
		$this->pageOgImage = $model->getImageUrl(0, 0, true);

		$this->render('view',
			array(
				'model' => $model,
			));
	}

	public function actionCreateTorrent ( $gId ) {
		$TorrentGroup = $this->loadModel($gId);

		$title = Yii::t('torrentsModule.common',
			'Добавление торрента в группу "{title}"',
			array('{title}' => $TorrentGroup->getTitle()));
		$this->breadcrumbs[] = $title;
		$this->pageTitle = $title;

		$Torrent = new models\Torrent();

		$Category = \Category::model()->findByPk($TorrentGroup->cId);
		if ( !$Category ) {
			throw new \CHttpException(404);
		}

		$Attributes = $Category->attrs(array('condition' => 'common = 0'));

		if ( isset($_POST[$Torrent->resolveClassName()]) ) {
			$Torrent->info_hash = \CUploadedFile::getInstance($Torrent, 'info_hash');
			$Torrent->setTags($_POST['torrentTags']);

			$TorrentGroup->addTags($_POST['torrentTags']);

			$valid = $Torrent->validate();
			$valid = $this->validateAttributes($Attributes, $TorrentGroup) && $valid;

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {

					$this->processAttributes($Torrent);

					$Torrent->gId = $TorrentGroup->getId();
					$Torrent->save(false);

					$TorrentGroup->mtime = time();
					$TorrentGroup->save(false);

					//foreach ( $Attributes AS $Attribute ) {

					//}

					$transaction->commit();

					Yii::app()->getUser()->setFlash(\User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно добавлен'));
					$this->redirect(CMap::mergeArray($TorrentGroup->getUrl(),
						array('#' => 'torrent' . $Torrent->getId())));
				} catch ( \CException $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), \CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(\User::FLASH_ERROR,
						Yii::t('torrentsModule.common',
							'При создании торента возникли проблемы, пожалуйста, попробуйте позже.'));
				}
			}

		}

		$this->render('createTorrent',
			array(
				'torrentGroup' => $TorrentGroup,
				'torrent'      => $Torrent,
				'attributes'   => $Attributes,
			));
	}

	public function actionDownload ( $id ) {
		$Torrent = models\Torrent::model()->findByPk($id);

		if ( !$Torrent ) {
			throw new \CHttpException(404, 'The requested page does not exist.');
		}

		$url = $Torrent->torrentGroup->getUrl();
		$torrent = new tComponents\TorrentComponent($Torrent->getDownloadPath());
		$torrent->comment(Yii::app()->createAbsoluteUrl(array_shift($url), $url));
		$torrent->announce(array($Torrent->getAnnounce()));
		$torrent->send($Torrent->getTitle() . '.torrent');
	}

	public function actionCreateGroup ( $cId ) {
		$title = Yii::t('torrentsModule.common', 'Загрузка торрента');
		$this->breadcrumbs[] = $title;
		$this->pageTitle = $title;

		$TorrentGroup = new models\TorrentGroup();
		$Torrent = new models\Torrent();
		$Category = \Category::model()->findByPk($cId);
		if ( !$Category ) {
			throw new \CHttpException(404);
		}

		$Attributes = $Category->attrs;

		$TorrentGroup->cId = $Category->getId();

		if ( isset($_POST[$TorrentGroup->resolveClassName()]) ) {
			$TorrentGroup->attributes = $_POST[$TorrentGroup->resolveClassName()];
			$Torrent->info_hash = \CUploadedFile::getInstance($Torrent, 'info_hash');
			$Torrent->setTags($_POST['torrentTags']);

			$TorrentGroup->setTags($_POST['torrentTags']);

			$valid = $TorrentGroup->validate();
			$valid = $Torrent->validate() && $valid;


			if ( $Category->attrs ) {
				$Attributes = $Category->attrs;
				$valid = $this->validateAttributes($Attributes, $TorrentGroup) && $valid;
			}

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					foreach ( $Attributes AS $Attribute ) {
						if ( $Attribute->common ) {
							$this->processAttributes($TorrentGroup, $Attribute->id);
						}
						else {
							$this->processAttributes($Torrent, $Attribute->id);
						}
					}

					$TorrentGroup->save(false);

					$Torrent->gId = $TorrentGroup->getId();
					$Torrent->save(false);

					$transaction->commit();

					Yii::app()->getUser()->setFlash(\User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно добавлен'));
					$this->redirect(CMap::mergeArray($TorrentGroup->getUrl(),
						array('#' => 'torrent' . $Torrent->getId())));
				} catch ( \CException $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), \CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(\User::FLASH_ERROR,
						Yii::t('torrentsModule.common',
							'При создании торента возникли проблемы, пожалуйста, попробуйте позже.'));
				}
			}

		}

		$this->render('createGroup',
			array(
				'torrentGroup' => $TorrentGroup,
				'category'     => $Category,
				'torrent'      => $Torrent,
				'attributes'   => $Attributes,
				'cId'          => $cId
			));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate () {
		$title = Yii::t('torrentsModule.common', 'Загрузка торрента');
		$this->breadcrumbs[] = $title;
		$this->pageTitle = $title;

		Yii::import('application.modules.category.models.*');

		$model = new models\TorrentGroup('upload');
		$category = new \Category('createTorrent');

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation(array($model, $category));

		if ( isset($_GET[$model->resolveClassName()]) ) {
			$model->attributes = $_GET[$model->resolveClassName()];
			$category->attributes = $_GET[$category->resolveClassName()];

			$gId = Yii::app()->getRequest()->getParam('gId', 0);

			$valid = $category->validate();
			$valid = $model->validate() && $valid;
			if ( $valid ) {
				if ( $gId ) {
					$this->redirect(array(
						'createTorrent',
						'gId' => $gId,
						'cId' => $category->getId()
					));
				}
				else {
					$this->redirect(array(
						'createGroup',
						'cId' => $category->getId()
					));
				}
			}
		}

		$this->render('create',
			array(
				'model'    => $model,
				'category' => $category,
			));
	}

	public function actionUpdateGroup ( $id ) {
		$TorrentGroup = $this->loadModel($id);

		if ( !Yii::app()->user->checkAccess('updateOwnTorrentGroup',
				array('model' => $TorrentGroup)) && !Yii::app()->user->checkAccess('updateTorrentGroup')
		) {
			throw new \CHttpException(403);
		}

		$title = Yii::t('torrentsModule.common',
			'Редактирование группы "{title}"',
			array('{title}' => $TorrentGroup->getTitle()));
		$this->breadcrumbs = array(
			$TorrentGroup->getTitle() => $TorrentGroup->getUrl(),
			$title
		);
		$this->pageTitle = $title;

		$Attributes = $TorrentGroup->getEavAttributeKeys();

		if ( isset($_POST[$TorrentGroup->resolveClassName()]) ) {
			$TorrentGroup->attributes = $_POST[$TorrentGroup->resolveClassName()];

			$valid = $TorrentGroup->validate();


			if ( $Attributes ) {
				$valid = $this->validateAttributes($Attributes, $TorrentGroup) && $valid;
			}

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$this->processAttributes($TorrentGroup);

					$TorrentGroup->title = '';
					$TorrentGroup->save(false);

					$transaction->commit();

					Yii::app()->getUser()->setFlash(\User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно добавлен'));
					$this->redirect($TorrentGroup->getUrl());
				} catch ( \CException $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), \CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(\User::FLASH_ERROR,
						Yii::t('torrentsModule.common',
							'При редактировании торента возникли проблемы, пожалуйста, попробуйте позже.'));
				}
			}

		}

		$this->render('updateGroup',
			array(
				'torrentGroup' => $TorrentGroup,
				'attributes'   => $Attributes,
			));

	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateTorrent ( $id ) {
		$Torrent = models\Torrent::model()->findByPk($id);

		if ( !$Torrent ) {
			throw new \CHttpException(404);
		}

		if ( !Yii::app()->user->checkAccess('updateOwnTorrent',
				array('model' => $Torrent)) && !Yii::app()->user->checkAccess('updateTorrent')
		) {
			throw new \CHttpException(403);
		}

		$TorrentGroup = $Torrent->torrentGroup;

		$title = Yii::t('torrentsModule.common',
			'Редактирование торрента "{title}"',
			array('{title}' => $Torrent->getTitle()));
		$this->breadcrumbs = array(
			$TorrentGroup->getTitle() => $TorrentGroup->getUrl(),
			$title
		);
		$this->pageTitle = $title;


		$Category = \Category::model()->findByPk($TorrentGroup->cId);
		if ( !$Category ) {
			throw new \CHttpException(404);
		}

		$Attributes = $Category->attrs(array('condition' => 'common = 0'));

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset($_POST[$Torrent->resolveClassName()]) ) {
			$Torrent->info_hash = \CUploadedFile::getInstance($Torrent, 'info_hash');

			$TorrentGroup->removeTags($Torrent->getTags());

			$Torrent->setTags($_POST['torrentTags']);

			$TorrentGroup->addTags($_POST['torrentTags']);

			$valid = $Torrent->validate();
			$valid = $this->validateAttributes($Attributes, $TorrentGroup) && $valid;

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$this->processAttributes($Torrent);

					$Torrent->title = '';
					$Torrent->gId = $TorrentGroup->getId();
					$Torrent->save(false);

					if ( $Torrent->info_hash instanceof \CUploadedFile ) {
						$TorrentGroup->mtime = time();
					}
					$TorrentGroup->save(false);

					//foreach ( $Attributes AS $Attribute ) {

					//}

					$transaction->commit();

					Yii::app()->getUser()->setFlash(\User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно изменен'));
					$this->redirect(CMap::mergeArray($TorrentGroup->getUrl(),
						array('#' => 'torrent' . $Torrent->getId())));
				} catch ( \CException $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), \CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(\User::FLASH_ERROR,
						Yii::t('torrentsModule.common',
							'При редактирование торента возникли проблемы, пожалуйста, попробуйте позже.'));
				}
			}

		}


		$this->render('createTorrent',
			array(
				'torrent'      => $Torrent,
				'torrentGroup' => $TorrentGroup,
				'attributes'   => $Attributes
			));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete ( $id ) {
		$model = models\TorrentGroup::model()->findByPk($id);
		if ( $model === null ) {
			throw new \CHttpException(404, Yii::t('torrentsModule.common', 'Группа торрентов не найдена'));
		}

		if ( !Yii::app()->user->checkAccess('deleteOwnTorrentGroup',
				array('model' => $model)) && !Yii::app()->user->checkAccess('deleteTorrentGroup')
		) {
			throw new \CHttpException(403);
		}


		if ( $model->delete() ) {
			Yii::app()->getUser()->setFlash(\User::FLASH_SUCCESS,
				Yii::t('torrentsModule.common',
					'Группа торрентов удалена успешно'));
		}
		else {
			Yii::app()->getUser()->setFlash(\User::FLASH_ERROR,
				Yii::t('torrentsModule.common',
					'При удалении группы торрентов возникли ошибки'));
		}
		$this->redirect(array('/torrents/default/index'));
	}

	public function actionDeleteTorrent ( $id ) {
		// we only allow deletion via POST request
		$model = models\Torrent::model()->findByPk($id);
		if ( $model === null ) {
			throw new \CHttpException(404, Yii::t('torrentsModule.common', 'Торрент не найден'));
		}

		if ( !Yii::app()->user->checkAccess('deleteOwnTorrent',
				array('model' => $model)) && !Yii::app()->user->checkAccess('deleteTorrent')
		) {
			throw new \CHttpException(403);
		}

		$url = $model->torrentGroup->getUrl();

		if ( $model->delete() ) {
			Yii::app()->getUser()->setFlash(\User::FLASH_SUCCESS,
				Yii::t('torrentsModule.common',
					'Торрент удален успешно'));
		}
		else {
			Yii::app()->getUser()->setFlash(\User::FLASH_ERROR,
				Yii::t('torrentsModule.common',
					'При удалении торрента возникли ошибки'));
		}

		$this->redirect($url);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex () {
		$title = Yii::t('torrentsModule.common', 'Торренты');
		$this->pageTitle = $title;

		$view = Yii::app()->getRequest()->getParam('view', Yii::app()->getUser()->getState('view', 'list'));

		if ( isset($_GET['pageSize']) ) {
			Yii::app()->getUser()->setState('pageSize', (int) $_GET['pageSize']);
			unset($_GET['pageSize']); // would interfere with pager and repetitive page size change
		}

		$pageSize = Yii::app()->user->getState('pageSize', Yii::app()->config->get('torrentsModule.pageSize'));

		if ( $view == 'list' ) {
			Yii::app()->getUser()->setState('view', 'list');

			$model = new models\TorrentGroup();

			$model->unsetAttributes(); // clear any default values
			$model->setScenario('search');
			$model->setSearchSettings();

			$attributes = Yii::app()->getRequest()->getQuery('TorrentGroup', '');

			//$model->title = $search;
			$model->attributes = $attributes;

			$dataProvider = $model->search();

			\Ajax::renderAjax('index',
				array(
					'dataProvider' => $dataProvider,
				    'pageSize' => $pageSize,

				),
				false,
				false,
				true);
		}
		else {
			Yii::app()->getUser()->setState('view', 'grid');

			$model = new models\Torrent();

			$model->unsetAttributes(); // clear any default values
			$model->setScenario('search');
			$model->setSearchSettings();

			$attributes = Yii::app()->getRequest()->getQuery('Torrent', '');

			//$model->title = $search;
			$model->attributes = $attributes;

			$dataProvider = $model->search();

			\Ajax::renderAjax('indexGrid',
				array(
					'dataProvider' => $dataProvider,
					'pageSize' => $pageSize,
				),
				false,
				false,
				true);
		}
	}

	public function actionSuggest ( $term, $category ) {
		$TorrentGroup = new models\TorrentGroup();
		$Category = \Category::model()->findByPk($category);

		if ( !$Category ) {
			\Ajax::send(\Ajax::AJAX_WARNING,
				Yii::t('torrentsModule.common',
					'Сначала выберете категорию, а после этого заполните поле "{fieldName}"',
					array('{fieldName}' => $TorrentGroup->getAttributeLabel('title'))));
		}

		$criteria = new CDbCriteria();
		$criteria->condition = 'cId = :cId';
		$criteria->params = array(
			'cId' => $category,
		);
		$criteria->limit = 50;
		$criteria->order = 'mtime DESC';

		$TorrentGroup->searchWithText($term);
		$models = $TorrentGroup->findAll($criteria);

		$return = array();

		foreach ( $models AS $model ) {
			$return[] = array(
				'id'   => $model->getId(),
				'text' => $model->getTitle(),
			);
		}
		\Ajax::send(\Ajax::AJAX_SUCCESS, 'ok', array('titles' => $return));
	}

	public function actionFileList ( $id ) {
		$Torrent = models\Torrent::model()->findByPk($id);

		if ( !$Torrent ) {
			throw new CHttpException(404);
		}

		$torrent = new tComponents\TorrentComponent($Torrent->getDownloadPath());
		$contents = $torrent->content();
		ksort($contents);

		$data = array();

		foreach ( $contents AS $filename => $size ) {
			$data[] = array(
				'filename' => $filename,
				'size'     => $size
			);
		}

		$dataProvider = new CArrayDataProvider($data, array(
			'sort'       => array(
				'attributes' => array(
					'filename',
					'size'
				),
			),

			'pagination' => array(
				'pageSize' => 50,
			),
		));

		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.min.js'] = false;

		\Ajax::renderAjax('fileList',
			array(
				'dataProvider' => $dataProvider,
				'model'        => $Torrent
			),
			false,
			true,
			true);
	}

	public function actionTagsSuggest ( $q ) {
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('t.name', $q, true);
		$criteria->group = 't.name';
		$criteria->limit = 50;
		$criteria->order = 'count DESC';
		$tags = models\Torrent::model()->getAllTags($criteria);

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
	 * Save model attributes
	 *
	 * @param         $model
	 * @param integer $k
	 *
	 * @return boolean
	 */
	protected function processAttributes ( $model, $key = false ) {
		$attributes = Yii::app()->request->getPost('Attribute', array());

		if ( $key !== false ) {
			$val = (isset($attributes[$key]) ? $attributes[$key] : '');
			$attributes = array();
			$attributes[$key] = $val;
		}

		if ( empty($attributes) ) {
			return false;
		}
		$attributes = new CMap($attributes);

		if ( $key ) {
			$model->deleteEavAttributes(array($key), true);
		}
		else {
			$model->deleteEavAttributes(array(), true);
		}
		//var_dump($attributes);
		$purify = new CHtmlPurifier();
		// Delete empty values
		foreach ( $attributes AS $key => $val ) {
			if ( is_string($val) && $val === '' ) {
				$attributes->remove($key);
			}
			else {
				$attributes[$key] = trim($purify->purify($val));
			}
		}

		return $model->setEavAttributes($attributes->toArray());
	}

	/**
	 * Validate required store attributes
	 *
	 * @param array               $attributes
	 * @param models\TorrentGroup $TorrentGroup
	 * @param bool                $sendJson
	 *
	 * @return bool
	 */
	public function validateAttributes ( &$attributes, models\TorrentGroup $TorrentGroup, $sendJson = false ) {
		if ( !$attributes ) {
			return true;
		}

		$errors = false;
		$jsonErrors = array();
		foreach ( $attributes AS $attr ) {
			if ( $attr->required && $_POST['Attribute'][$attr->id] === '' ) {
				$errors = true;
				if ( !$sendJson ) {
					$attr->addError('title',
						Yii::t('yii', '{attribute} cannot be blank.', array('{attribute}' => $attr->title)));
				}
				else {
					$jsonErrors['Attribute_' . $attr->id][] = Yii::t('yii',
						'{attribute} cannot be blank.',
						array('{attribute}' => $attr->title));
				}
			}
			if ( $attr->validator ) {
				$validator = \CValidator::createValidator($attr->validator, $attr, $attr->id);
				$validator->validate($attr, $attr->id);

				if ( $errorsText = $attr->getErrors() ) {
					$errors = true;
					if ( $sendJson ) {
						$attr->clearErrors();
						$jsonErrors['Attribute_' . $attr->id] = $errorsText;
					}
				}
			}
		}

		/**
		 * Проверка на уникальность
		 */

		if ( $TorrentGroup->getIsNewRecord() ) {
			$findAttributes = array();
			$uniqueAttributes = $TorrentGroup->getTitleAttributes();
			foreach ( $uniqueAttributes AS $uniqueAttribute ) {
				if ( !empty($_POST['Attribute'][$uniqueAttribute['attrId']]) ) {
					$findAttributes[$uniqueAttribute['attrId']] = trim($_POST['Attribute'][$uniqueAttribute['attrId']]);
				}
			}

			if ( $findAttributes ) {
				$model = models\TorrentGroup::model()->withEavAttributes($findAttributes)->find();
				if ( $model ) {
					$firstKey = array_shift(array_keys($findAttributes));

					$errorText = Yii::t('torrentsModule.common',
						'Торрент с названием "{title}" уже есть на трекере, он называется "{newTitle}" (кликните, чтобы посмотреть). Если вы загружаете такой же торрент, то кликните {here}, чтобы добавить ваш торрент в группу "{newTitle}".',
						array(
							'{title}'    => \CHtml::encode($_POST['Attribute'][$firstKey]),
							'{newTitle}' => \CHtml::link($model->getTitle(),
									$model->getUrl(),
									array('target' => '_blank')),
							'{here}'     => \CHtml::link(Yii::t('torrentsModule.common', 'здесь'),
									array(
										'/torrents/default/createTorrent',
										'gId' => $model->getId()
									))
						));

					$errors = true;

					if ( !$sendJson ) {
						$attributes[$firstKey]->addError('title', $errorText);
					}
					else {
						$jsonErrors['Attribute_' . $firstKey][] = $errorText;
					}
				}
			}
		}
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'torrent-form' ) {
			echo CJSON::encode($jsonErrors);
			Yii::app()->end();
		}
		else {
			return !$errors;
		}
	}

	/**
	 * @param $id
	 *
	 * @return \modules\torrents\models\TorrentGroup
	 * @throws \CHttpException
	 */
	public function loadModel ( $id ) {
		$model = models\TorrentGroup::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param \CModel the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'torrent-form' ) {
			echo \CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


	public function getPagesForSitemap () {
		return models\TorrentGroup::model();
	}
}
