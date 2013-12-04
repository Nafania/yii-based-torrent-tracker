<?php

class DefaultController extends components\Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + loadAnswerBlock'));
	}

	public function beforeAction ( $action ) {
		parent::beforeAction($action);

		$title = Yii::t('pmsModule.common', 'Личные сообщения');

		if ( $action->getId() == 'index' ) {
			$this->breadcrumbs[] = $title;
		}
		else {
			$this->breadcrumbs[$title] = array('index');
		}

		return true;
	}

	public function actionCreate () {
		$title = Yii::t('pmsModule.common', 'Создание личного сообщения');
		$this->breadcrumbs[] = $title;
		$this->pageTitle = $title;

		$model = new PrivateMessage();
		$usersData = array();
		$models = array();

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if ( isset($_POST['PrivateMessage']) ) {
			$receivers = explode(',', $_POST['PrivateMessage']['receiverUid']);

			$usersData = $this->getUsersData($receivers);

			$valid = true;

			foreach ( $receivers AS $receiver ) {
				$model->attributes = $_POST['PrivateMessage'];

				$_model = new PrivateMessage();
				$_model->attributes = $_POST['PrivateMessage'];
				$_model->receiverUid = $receiver;

				$valid = $_model->validate() && $valid;

				$models[] = $_model;
			}

			if ( $valid ) {
				$transaction = $model->getDbConnection()->beginTransaction();

				try {
					foreach ( $models AS $_model ) {
						$_model->save(false);

						if ( !$_model->branch ) {
							$_model->branch = $_model->getId();
							$_model->save(false);
						}
					}

					$transaction->commit();

					$message = Yii::t('pmsModule.common', 'Личное сообщение успешно отправлено');
					if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
						Ajax::send(Ajax::AJAX_SUCCESS,
							$message,
							array(
							     'id'   => $_model->getId(),
							     'view' => $this->renderPartial('_view',
								     array(
								          'model' => $_model,
								     ),
								     true,
								     false)
							));
					}
					else {
						Yii::app()->user->setFlash(User::FLASH_SUCCESS, $message);
						$this->redirect(array('index'));
					}
				} catch ( CException $e ) {
					$transaction->rollback();
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					$message = Yii::t('pmsModule.common',
						'При отправке возникли ошибки. Попробуйте отправить сообщение позднее.');

					if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
						Ajax::send(Ajax::AJAX_ERROR, $message);
					}
					else {
						Yii::app()->user->setFlash(User::FLASH_ERROR, $message);
					}

				}
			}
		}

		$this->render('create',
			array(
			     'model'     => $model,
			     'models'    => $models,
			     'usersData' => $usersData,
			));
	}

	public function actionIndex () {
		$this->pageTitle = Yii::t('pmsModule.common', 'Личные сообщения');

		$model = new PrivateMessage();

		$model->branches()->own();
		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$attributes = Yii::app()->getRequest()->getQuery('PrivateMessage', '');
		$model->attributes = $attributes;

		$dataProvider = $model->search();

		$this->render('index',
			array(
			     'dataProvider' => $dataProvider,
			     'model'        => $model,
			));
	}

	public function actionView ( $id ) {
		$models = PrivateMessage::model()->viewBranch()->own()->findAllByAttributes(array('branch' => $id));
		if ( !$models ) {
			throw new CHttpException(404);
		}

		$title = Yii::t('pmsModule.common', 'Личное сообщение "{title}"', array('{title}' => $models[0]->getTitle()));

		$this->pageTitle = $title;
		$this->breadcrumbs[] = $title;

		PrivateMessage::model()->updateAll(array('readed' => PrivateMessage::READED),
			array(
			     'condition' => 'branch = :branch AND readed = :readed AND receiverUid = :receiverUid',
			     'params'    => array(
				     ':branch'      => $id,
				     ':receiverUid' => Yii::app()->getUser()->getId(),
				     ':readed'      => PrivateMessage::UNREADED
			     )
			));

		$models = PrivateMessage::buildTree($models);

		$pm = new PrivateMessage();

		$this->render('tree',
			array(
			     'models' => $models,
			     'pm'     => $pm,
			));
	}


	public function actionLoadAnswerBlock () {
		$parentId = Yii::app()->getRequest()->getParam('parentId', 0);

		$model = new PrivateMessage();
		$model->parentId = $parentId;

		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;

		$view = $this->renderPartial('branchAnswer',
			array(
			     'model' => $model,
			),
			true,
			true);

		Ajax::send(Ajax::AJAX_SUCCESS,
			'ok',
			array(
			     'view' => $view,
			));
	}

	public function actionDelete () {
		$ids = Yii::app()->getRequest()->getParam('id', array());

		try {
			$criteria = new CDbCriteria();
			$criteria->index = 'branch';

			$models = PrivateMessage::model()->own()->findAllByPk($ids, $criteria);
			$models = PrivateMessage::model()->own()->findAllByAttributes(array('branch' => array_keys($models)));

			foreach ( $models AS $model ) {
				$model->deletedBy = Yii::app()->getUser()->getId();
				$model->save(false);
			}

			Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('pmsModule.common', 'Выбранные сообщения успешно удалены'));
		} catch ( CException $e ) {
			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
			Ajax::send(Ajax::AJAX_ERROR, Yii::t('pmsModule.common', 'При удалении сообщений произошла ошибка'));
		}
	}

	public function loadModel ( $id ) {
		$model = PrivateMessage::model()->findAllBy($id);
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
		if ( isset($_POST['ajax']) ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * @param $receivers
	 *
	 * @return array
	 */
	protected function getUsersData ( $receivers ) {
		$ret = array();

		$models = User::model()->findAllByPk($receivers);
		foreach ( $models AS $model ) {
			$ret[] = array(
				'id'   => $model->getId(),
				'text' => $model->getName(),
			);
		}

		return $ret;
	}

	/**
	 * Метод для проверки формы. Нужен, чтобы не показывать одинаковые сообщения об ошибках при отслыке ЛС
	 * @param       $model
	 * @param array $htmlOptions
	 *
	 * @return string
	 */
	public function errorSummary ( $model, $htmlOptions = array() ) {
		$content = '';
		if ( !is_array($model) ) {
			$model = array($model);
		}
		$errorMessages = array();
		foreach ( $model as $m ) {
			foreach ( $m->getErrors() as $errors ) {
				foreach ( $errors as $error ) {
					if ( $error != '' && !in_array($error, $errorMessages) ) {
						$errorMessages[] = $error;
						$content .= "<li>$error</li>\n";
					}
				}
			}
		}
		if ( $content !== '' ) {
			$header = '<p>' . Yii::t('yii', 'Please fix the following input errors:') . '</p>';
			return CHtml::tag('div', $htmlOptions, $header . "\n<ul>\n$content</ul>");
		}
		else {
			return '';
		}
	}
}