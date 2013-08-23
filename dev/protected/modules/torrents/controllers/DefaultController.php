<?php

class DefaultController extends Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
		//return CMap::mergeArray(parent::filters(),
		return array(
			'postOnly + delete',
			'ajaxOnly + fileList, tagsSuggest',
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

		$title = Yii::t('torrentsModule.common', '{torrentName}', array('{torrentName}' => $model->getTitle()));
		$this->breadcrumbs = array(
			Yii::t('torrentsModule.common', 'Торренты') => array('index'),
			$title
		);
		$this->pageTitle = $title;

		$this->render('view',
			array(
			     'model' => $model,
			));
	}

	public function actionCreateTorrent ( $gId ) {
		$TorrentGroup = $this->loadModel($gId);

		$title = Yii::t('torrentsModule.common',
			'Загрузка торрента "{title}"',
			array('{title}' => $TorrentGroup->getTitle()));
		$this->breadcrumbs = array(
			Yii::t('torrentsModule.common', 'Торренты') => array('index'),
			$title
		);
		$this->pageTitle = $title;

		$Torrent = new Torrent();

		$Category = Category::model()->findByPk($TorrentGroup->cId);
		if ( !$Category ) {
			throw new CHttpException(404);
		}

		$Attributes = $Category->attrs(array('condition' => 'common = 0'));

		if ( isset($_POST['Torrent']) ) {
			$Torrent->info_hash = CUploadedFile::getInstance($Torrent, 'info_hash');
			$Torrent->setTags($_POST['tags']);

			$TorrentGroup->addTags($_POST['tags']);

			$valid = $Torrent->validate();
			$valid = $this->validateAttributes($Attributes) && $valid;

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$Torrent->gId = $TorrentGroup->getId();
					$Torrent->save(false);

					$TorrentGroup->mtime = time();
					$TorrentGroup->save(false);

					foreach ( $Attributes AS $Attribute ) {
						$this->processAttributes($Torrent, $Attribute->id);
					}

					$transaction->commit();

					Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно добавлен'));
					$this->redirect(CMap::mergeArray($TorrentGroup->getUrl(),
						array('#' => 'collapse' . $Torrent->getId())));
				} catch ( Exception $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
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
		$Torrent = Torrent::model()->findByPk($id);

		if ( !$Torrent ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		Yii::app()->getRequest()->sendFile($Torrent->getTitle() . '.torrent',
			file_get_contents($Torrent->getDownloadPath()),
			'application/x-bittorrent');
	}

	public function actionCreateGroup ( $cId ) {
		$title = Yii::t('torrentsModule.common', 'Загрузка торрента');
		$this->breadcrumbs = array(
			Yii::t('torrentsModule.common', 'Торренты') => array('index'),
			$title
		);
		$this->pageTitle = $title;

		$TorrentGroup = new TorrentGroup();
		$Torrent = new Torrent();
		$Category = Category::model()->findByPk($cId);
		if ( !$Category ) {
			throw new CHttpException(404);
		}

		$Attributes = $Category->attrs;

		if ( isset($_POST['TorrentGroup']) ) {
			$TorrentGroup->attributes = $_POST['TorrentGroup'];
			$Torrent->info_hash = CUploadedFile::getInstance($Torrent, 'info_hash');
			$Torrent->setTags($_POST['tags']);

			$TorrentGroup->setTags($_POST['tags']);

			$valid = $TorrentGroup->validate();
			$valid = $Torrent->validate() && $valid;


			if ( $Category->attrs ) {
				$Attributes = $Category->attrs;
				$valid = $this->validateAttributes($Attributes) && $valid;
			}

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$TorrentGroup->cId = $Category->getId();
					$TorrentGroup->save(false);

					$Torrent->gId = $TorrentGroup->getId();
					$Torrent->save(false);

					foreach ( $Attributes AS $Attribute ) {
						if ( $Attribute->common ) {
							$this->processAttributes($TorrentGroup, $Attribute->id);
						}
						else {
							$this->processAttributes($Torrent, $Attribute->id);
						}
					}

					$transaction->commit();

					Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно добавлен'));
					$this->redirect(CMap::mergeArray($TorrentGroup->getUrl(),
						array('#' => 'collapse' . $Torrent->getId())));
				} catch ( Exception $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
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
		$this->breadcrumbs = array(
			Yii::t('torrentsModule.common', 'Торренты') => array('index'),
			$title
		);
		$this->pageTitle = $title;

		Yii::import('application.modules.category.models.*');

		$model = new TorrentGroup('upload');
		$category = new Category('createTorrent');

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation(array($model, $category));

		if ( isset($_GET['TorrentGroup']) ) {
			$model->attributes = $_GET['TorrentGroup'];
			$category->attributes = $_GET['Category'];

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

		$title = Yii::t('torrentsModule.common',
			'Редактирование группы "{title}"',
			array('{title}' => $TorrentGroup->getTitle()));
		$this->breadcrumbs = array(
			Yii::t('torrentsModule.common', 'Торренты') => array('index'),
			$title
		);
		$this->pageTitle = $title;

		$Attributes = $TorrentGroup->getEavAttributeKeys();

		if ( isset($_POST['TorrentGroup']) ) {
			$TorrentGroup->attributes = $_POST['TorrentGroup'];

			$valid = $TorrentGroup->validate();


			if ( $Attributes ) {
				$valid = $this->validateAttributes($Attributes) && $valid;
			}

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$TorrentGroup->title = '';
					$TorrentGroup->save(false);

					$this->processAttributes($TorrentGroup);

					$transaction->commit();

					Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно добавлен'));
					$this->redirect($TorrentGroup->getUrl());
				} catch ( Exception $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
						Yii::t('torrentsModule.common',
							'При создании торента возникли проблемы, пожалуйста, попробуйте позже.'));
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
		$Torrent = Torrent::model()->findByPk($id);

		if ( !$Torrent ) {
			throw new CHttpException(404);
		}

		$TorrentGroup = $Torrent->torrentGroup;

		$title = Yii::t('torrentsModule.common',
			'Редактирование торрента "{title}"',
			array('{title}' => $TorrentGroup->getTitle()));
		$this->breadcrumbs = array(
			Yii::t('torrentsModule.common', 'Торренты') => array('index'),
			$title
		);
		$this->pageTitle = $title;


		$Category = Category::model()->findByPk($TorrentGroup->cId);
		if ( !$Category ) {
			throw new CHttpException(404);
		}

		$Attributes = $Category->attrs(array('condition' => 'common = 0'));

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset($_POST['Torrent']) ) {
			$Torrent->info_hash = CUploadedFile::getInstance($Torrent, 'info_hash');

			$TorrentGroup->removeTags($Torrent->getTags());

			$Torrent->setTags($_POST['tags']);

			$TorrentGroup->addTags($_POST['tags']);

			$valid = $Torrent->validate();
			$valid = $this->validateAttributes($Attributes) && $valid;

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$Torrent->title = '';
					$Torrent->gId = $TorrentGroup->getId();
					$Torrent->save(false);

					if ( $Torrent->info_hash instanceof CUploadedFile ) {
						$TorrentGroup->mtime = time();
					}
					$TorrentGroup->save(false);

					//foreach ( $Attributes AS $Attribute ) {
					$this->processAttributes($Torrent);
					//}

					$transaction->commit();

					Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
						Yii::t('torrentsModule.common', 'Торрент успешно изменен'));
					$this->redirect(CMap::mergeArray($TorrentGroup->getUrl(),
						array('#' => 'collapse' . $Torrent->getId())));
				} catch ( Exception $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
						Yii::t('torrentsModule.common',
							'При создании торента возникли проблемы, пожалуйста, попробуйте позже.'));
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
		if ( Yii::app()->request->isPostRequest ) {
			// we only allow deletion via POST request
			$model = TorrentGroup::model()->findByPk($id);
			if ( $model === null ) {
				Ajax::send(Ajax::AJAX_ERROR, Yii::t('torrentsModule.common', 'Группа торрентов не найдена'));
			}


			if ( $model->delete() ) {
				Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
					Yii::t('torrentsModule.common',
						'Группа торрентов удалена успешно'));
				Ajax::send(Ajax::AJAX_SUCCESS,
					Yii::t('torrentsModule.common', 'Группа торрентов удалена успешно'),
					array(
					     'location' => Yii::app()->createUrl('/torrents/default/index')
					));
			}
			else {
				Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
					Yii::t('torrentsModule.common',
						'При удалении группы торрентов возникли ошибки'));
				Ajax::send(Ajax::AJAX_ERROR,
					Yii::t('torrentsModule.common', 'При удалении группы торрентов возникли ошибки'));
			}
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, Yii::t('torrentsModule.common', 'Группа торрентов не найдена'));
		}
	}

	public function actionDeleteTorrent ( $id ) {
		if ( Yii::app()->request->isPostRequest ) {
			// we only allow deletion via POST request
			$model = Torrent::model()->findByPk($id);
			if ( $model === null ) {
				Ajax::send(Ajax::AJAX_ERROR, Yii::t('torrentsModule.common', 'Торрент не найден'));
			}

			if ( $model->delete() ) {
				$url = $model->torrentGroup->getUrl();
				Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
					Yii::t('torrentsModule.common',
						'Торрент удален успешно'));
				Ajax::send(Ajax::AJAX_SUCCESS,
					Yii::t('torrentsModule.common', 'Торрент удален успешно'),
					array(
					     'location' => Yii::app()->createUrl(array_shift($url), $url)
					));
			}
			else {
				Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
					Yii::t('torrentsModule.common',
						'При удалении торрента возникли ошибки'));
				Ajax::send(Ajax::AJAX_ERROR, Yii::t('torrentsModule.common', 'При удалении торрента возникли ошибки'));
			}
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, Yii::t('torrentsModule.common', 'Торрент не найден'));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex () {
		$title = Yii::t('torrentsModule.common', 'Торренты');
		$this->breadcrumbs = array(
			$title
		);
		$this->pageTitle = $title;

		$model = new TorrentGroup();

		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$attributes = Yii::app()->getRequest()->getQuery('TorrentGroup', '');
		$search = Yii::app()->getRequest()->getParam('search', '');
		$tags = Yii::app()->getRequest()->getParam('tags', '');
		$category = Yii::app()->getRequest()->getParam('category', '');

		//$model->title = $search;
		$model->attributes = $attributes;
		$model->searchWithText($search);
		$model->searchWithTags($tags);
		$model->searchWithCategory($category);

		$dataProvider = $model->search();

		Ajax::renderAjax('index', array(
		                             'dataProvider' => $dataProvider

		                          ), false, false, true);

	}

	public function actionSuggest ( $term, $category ) {
		$TorrentGroup = new TorrentGroup();
		$Category = Category::model()->findByPk($category);

		if ( !$Category ) {
			throw new CHttpException(404);
		}

		$models = $TorrentGroup->withEavAttributes(array($term))->findAllByAttributes(array('cId' => $category));

		$return = array();

		foreach ( $models AS $model ) {
			$return[] = array(
				'id'    => $model->getId(),
				'title' => $model->getTitle(),
			);
		}
		Ajax::send(Ajax::AJAX_SUCCESS, 'ok', $return);
	}

	public function actionFileList ( $id ) {
		$Torrent = Torrent::model()->findByPk($id);

		if ( !$Torrent ) {
			throw new CHttpException(404);
		}

		$torrent = new TorrentComponent($Torrent->getDownloadPath());
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

		Ajax::renderAjax('fileList', array('dataProvider' => $dataProvider), false, true, true);
	}

	public function actionTagsSuggest ( $q ) {
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('t.name', $q, true);
		$criteria->group = 't.name';
		$tags = Torrent::model()->getAllTags($criteria);

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

		$deleteModel = $model::model()->findByPk($model->getId());

		if ( $key ) {
			$deleteModel->deleteEavAttributes(array($key), true);
		}
		else {
			$deleteModel->deleteEavAttributes(array(), true);
		}

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

		return $model->setEavAttributes($attributes->toArray(), true);
	}

	/**
	 * Validate required store attributes
	 *
	 * @param array   $attributes
	 * @param integer $k
	 *
	 * @return bool
	 */
	public function validateAttributes ( &$attributes ) {
		if ( !$attributes ) {
			return true;
		}

		$errors = false;
		foreach ( $attributes AS $attr ) {
			if ( $attr->required && $_POST['Attribute'][$attr->id] === '' ) {
				$errors = true;
				$attr->addError('title',
					Yii::t('yii', '{attribute} cannot be blank.', array('{attribute}' => $attr->title)));
			}
			if ( $attr->validator ) {
				$validator = CValidator::createValidator($attr->validator, $attr, $attr->id);
				$validator->validate($attr, $attr->id);

				if ( $errorsText = $attr->getErrors() ) {
					$errors = true;
				}
			}
		}

		return !$errors;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel ( $id ) {
		$model = TorrentGroup::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'torrent-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
