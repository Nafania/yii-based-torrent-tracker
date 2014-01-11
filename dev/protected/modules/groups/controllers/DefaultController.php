<?php

class DefaultController extends components\Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
		return CMap::mergeArray(parent::filters(),
			array(
			     'postOnly + delete',
			     'ajaxOnly + join, unJoin, changeMemberStatus, invite',
			));
	}

	/**
	 * Displays a particular model.
	 *
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView ( $id ) {

		$model = $this->loadModel($id);

		$this->pageTitle = Yii::t('GroupsModule.common',
			'Просмотр группы "{groupName}"',
			array('{groupName}' => $model->getTitle(false)));
		$this->breadcrumbs[Yii::t('GroupsModule.common', 'Просмотр групп')] = array('index');
		$this->breadcrumbs[] = Yii::t('GroupsModule.common',
			'Просмотр группы "{groupName}"',
			array('{groupName}' => $model->getTitle(false)));

		$blogPost = new modules\blogs\models\BlogPost('search');
		$blogPost->forGroup($id);

		/**
		 * Если текущий юзер не участник группы, то показываем только не скрытые посты
		 */
		if ( !Group::checkJoin($model) ) {
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
			     'model'         => $model,
			     'postsProvider' => $postsProvider
			));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate () {
		$this->pageTitle = Yii::t('GroupsModule.common', 'Создание группы');
		$this->breadcrumbs[] = Yii::t('GroupsModule.common', 'Создание группы');

		$model = new Group();
		$this->performAjaxValidation($model);

		if ( isset($_POST['Group']) ) {
			$model->attributes = $_POST['Group'];
			$valid = $model->validate();

			if ( $valid ) {
				$transaction = Yii::app()->getDb()->beginTransaction();

				try {
					$model->save(false);

					$groupUser = new GroupUser();
					$groupUser->idUser = Yii::app()->getUser()->getId();
					$groupUser->idGroup = $model->getId();
					$groupUser->status = GroupUser::STATUS_APPROVED;
					$groupUser->save(false);

					$transaction->commit();

					Yii::app()->user->setFlash(User::FLASH_SUCCESS,
						Yii::t('GroupsModule.common', 'Группа создана успешно'));
					$this->redirect($model->getUrl());

				} catch ( Exception $e ) {
					$transaction->rollBack();

					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
					Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
						Yii::t('GroupsModule.common',
							'При создании группы возникли проблемы, пожалуйста, попробуйте позже.'));
				}
			}
		}

		$this->render('form',
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
		$this->pageTitle = Yii::t('GroupsModule.common', 'Редактирование группы');
		$this->breadcrumbs[] = Yii::t('GroupsModule.common', 'Редактирование группы');

		$model = $this->loadModel($id);

		if ( !Yii::app()->user->checkAccess('updateOwnGroup',
				array('ownerId' => $model->ownerId)) && !Yii::app()->user->checkAccess('updateGroup')
		) {
			throw new CHttpException(403);
		}

		$this->performAjaxValidation($model);

		if ( isset($_POST['Group']) ) {
			$model->attributes = $_POST['Group'];
			$valid = $model->validate();

			if ( $valid ) {

				try {
					$model->save(false);

					Yii::app()->user->setFlash(User::FLASH_SUCCESS,
						Yii::t('GroupsModule.common', 'Группа отредактирована успешно'));
					$this->redirect($model->getUrl());

				} catch ( Exception $e ) {
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
						Yii::t('GroupsModule.common',
							'При создании группы возникли проблемы, пожалуйста, попробуйте позже.'));
				}
			}
		}

		$this->render('form',
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
		$group = $this->loadModel($id);

		if ( !$group->delete() ) {
			throw new CHttpException(502, Yii::t('groupsModule.common',
				'Во время удаления группы произошли ошибки, пожалуйста, попробуйте позднее.'));
		}

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if ( !isset($_GET['ajax']) ) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex () {

		$this->pageTitle = Yii::t('GroupsModule.common', 'Просмотр групп');
		$this->breadcrumbs[] = Yii::t('GroupsModule.common', 'Просмотр групп');

		$model = new Group();
		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$attributes = Yii::app()->getRequest()->getQuery('attributes', '');
		$model->attributes = $attributes;

		$dataProvider = $model->visible()->search();

		Ajax::renderAjax('index',
			array(
			     'dataProvider' => $dataProvider,
			     'model'        => $model,
			),
			false,
			false,
			true);
	}


	public function actionMy () {
		$this->pageTitle = Yii::t('GroupsModule.common', 'Мои группы');
		$this->breadcrumbs[] = Yii::t('GroupsModule.common', 'Мои группы');


		$model = new Group();
		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$attributes = Yii::app()->getRequest()->getQuery('attributes', '');

		$model->attributes = $attributes;

		$criteria = new CDbCriteria();
		$criteria->together = true;
		$criteria->with = 'groupUsers';
		$criteria->addCondition('groupUsers.idUser = :idUser');
		$criteria->group = 'groupUsers.idGroup';
		$criteria->order = 'status = ' . GroupUser::STATUS_APPROVED . ' DESC';
		$criteria->params[':idUser'] = Yii::app()->getUser()->getId();
		//$criteria->params[':status'] = GroupUser::STATUS_APPROVED;
		//$criteria->addCondition('t.ownerId = :ownerId');
		//$criteria->params[':ownerId'] = Yii::app()->getUser()->getId();

		$dataProvider = $model->search();
		$dataProvider->getCriteria()->mergeWith($criteria);

		Ajax::renderAjax('my',
			array(
			     'dataProvider' => $dataProvider,
			     'model'        => $model,
			),
			false,
			false,
			true);
	}

	public function actionChangeMemberStatus ( $gId, $uId, $status = GroupUser::STATUS_NEW ) {
		$group = $this->loadModel($gId);

		if ( !Yii::app()->user->checkAccess('updateMembersStatusInOwnGroup',
				array('ownerId' => $group->ownerId)) && !Yii::app()->user->checkAccess('changeOwnStatus',
				array('uId' => $uId)) && !Yii::app()->user->checkAccess('updateGroup')
		) {
			throw new CHttpException(403);
		}

		$user = User::model()->findByPk($uId);

		if ( !$user ) {
			throw new CHttpException(404);
		}

		if ( $user->getId() == $group->ownerId ) {
			throw new CHttpException(403, Yii::t('GroupsModule.common', 'Нельзя изменить статус владельца группы'));
		}

		$groupUser = GroupUser::model()->findByAttributes(array(
		                                                       'idGroup' => $gId,
		                                                       'idUser'  => $uId,
		                                                  ));

		if ( !$groupUser ) {
			throw new CHttpException(404);
		}

		$groupUser->status = $status;

		if ( $groupUser->save() ) {
			Ajax::send(Ajax::AJAX_SUCCESS,
				Yii::t('GroupsModule.common',
					'Статус изменен успешно'),
					array(
					     'newStatus' => $groupUser->getStatusLabel(),
					));
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR,
				Yii::t('GroupsModule.common', 'Cant change status due errors'),
				$groupUser->getErrors());
		}
	}

	public function actionMembers ( $id ) {
		$group = $this->loadModel($id);

		$this->breadcrumbs = array(
			Yii::t('GroupsModule.common', 'Просмотр групп')      => array('index'),
			Yii::t('GroupsModule.common',
				'Просмотр группы "{groupName}"',
				array('{groupName}' => $group->getTitle(false))) => $group->getUrl(),
			Yii::t('GroupsModule.common',
				'Участники группы "{title}"',
				array('{title}' => $group->getTitle()))
		);
		$this->pageTitle = Yii::t('GroupsModule.common',
			'Участники группы "{title}"',
			array('{title}' => $group->getTitle()));

		$model = new User();
		$model->unsetAttributes(); // clear any default values
		$model->setScenario('search');

		$attributes = Yii::app()->getRequest()->getQuery('attributes', '');
		$status = Yii::app()->getRequest()->getQuery('status', GroupUser::STATUS_APPROVED);

		$model->attributes = $attributes;

		$criteria = new CDbCriteria();
		$criteria->with = 'groupUser';
		//$criteria->together = true;
		$criteria->addCondition('groupUser.idGroup = :idGroup');
		$criteria->addCondition('groupUser.status = :status');
		$criteria->params[':idGroup'] = $id;
		if ( Yii::app()->user->checkAccess('updateMembersStatusInOwnGroup',
				array('ownerId' => $group->ownerId)) || Yii::app()->user->checkAccess('updateGroup')
		) {
			$criteria->params[':status'] = (int) $status;
		}
		else {
			$criteria->params[':status'] = GroupUser::STATUS_APPROVED;
		}

		$model->getDbCriteria()->mergeWith($criteria);
		//$dataProvider = $model->search();

		Ajax::renderAjax('members',
			array(
			     //'dataProvider' => $dataProvider,
			     'model' => $model,
			     'group' => $group,
			),
			false,
			false,
			true);
	}

	public function actionSuggest ( $term ) {
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('name', $term);

		$users = User::model()->findAll($criteria);

		$result = array();
		foreach ( $users AS $user ) {
			$result[] = array(
				'id'   => $user->getId(),
				'text' => $user->getName(),
			);
		}
		Ajax::send(Ajax::AJAX_SUCCESS, 'ok', $result);
	}

	public function actionJoin () {
		$id = Yii::app()->getRequest()->getParam('id', 0);

		$model = $this->loadModel($id);
		if ( $model->getType() == Group::TYPE_CLOSED ) {
			throw new CHttpException(403, Yii::t('groupsModule.common',
				'Вы не можете вступить в закрытую группу. Попросите владельца группы пригласить вас.'));
		}

		$groupUser = new GroupUser();
		$groupUser->idGroup = $id;
		$groupUser->idUser = Yii::app()->getUser()->getId();

		if ( $groupUser->save() ) {
			Ajax::send(Ajax::AJAX_SUCCESS,
				Yii::t('GroupsModule.common',
					'Вы успешно подали заявку на вступление в группу.'),
				array( //'newText' => Yii::t('groupsModule.common', 'Выйти из группы'),
				));
		}
		else {
			Ajax::send(Ajax::AJAX_WARNING,
				Yii::t('GroupsModule.common',
					'При встпулении в группу возникли ошибки: {errors}',
					array('{errors}' => $groupUser->getError('idUser'))));
		}
	}

	public function actionUnJoin () {
		$id = Yii::app()->getRequest()->getParam('id', 0);

		$model = Group::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		if ( $model->ownerId == Yii::app()->getUser()->getId() ) {
			throw new CHttpException(404, Yii::t('GroupsModule.common',
				'Вы являетесь создателем группы и не можете из нее выйти'));
		}

		$groupUser = GroupUser::model()->findByAttributes(array(
		                                                       'idGroup' => $id,
		                                                       'idUser'  => Yii::app()->getUser()->getId(),
		                                                  ));

		if ( $groupUser && $groupUser->status == GroupUser::STATUS_DECLINED ) {
			throw new CHttpException(404, Yii::t('GroupsModule.common',
				'Вашу завяку на вступление отклонили в этой группе'));
		}
		if ( !$groupUser ) {
			throw new CHttpException(404, Yii::t('GroupsModule.common', 'Вы не состоите в этой группе'));
		}

		if ( $groupUser->delete() ) {
			Ajax::send(Ajax::AJAX_SUCCESS,
				Yii::t('groupsModule.common',
					'Вы успешно вышли из группы'),
				array(
				     'newText' => Yii::t('groupsModule.common', 'Вступить в группу'),
				));
		}
		else {
			Ajax::send(Ajax::AJAX_WARNING,
				Yii::t('GroupsModule.common',
					'При выходе из группы возникли ошибки.',
					$groupUser->getErrors()));
		}
	}

	public function actionInvite ( $id ) {
		$group = $this->loadModel($id);

		if ( !Yii::app()->user->checkAccess('inviteInOwnGroup',
				array('ownerId' => $group->ownerId)) && !Yii::app()->user->checkAccess('updateGroup')
		) {
			throw new CHttpException(403);
		}

		$groupUsers = array(new GroupUser('invite'));

		if ( isset($_POST['inviteUsers']) ) {
			$idUsers = explode(',', $_POST['inviteUsers']);

			$valid = true;

			$groupUsers = $result = array();

			foreach ( $idUsers AS $i => $idUser ) {
				$groupUser = new GroupUser('invite');
				$groupUser->idUser = (int) $idUser;
				$groupUser->idGroup = $group->getId();
				$groupUsers[] = $groupUser;
				$valid = $groupUser->validate() && $valid;

				foreach ( $groupUser->getErrors() AS $attribute => $errors ) {
					$result[CHtml::activeId($groupUser, '[' . $i . ']' . $attribute)] = $errors;
				}
			}

			if ( isset($_POST['ajax']) && $_POST['ajax'] === 'group-form' ) {
				echo CJSON::encode($result);
				Yii::app()->end();
			}

			if ( $valid ) {
				$transaction = Yii::app()->getDb()->beginTransaction();

				try {
					foreach ( $groupUsers AS $groupUser ) {
						$groupUser->status = GroupUser::STATUS_INVITED;
						$groupUser->save();
					}

					$transaction->commit();

					Ajax::send(Ajax::AJAX_SUCCESS,
						Yii::t('GroupsModule.common', 'Вы успешно пригласили в группу пользователей'));

				} catch ( Exception $e ) {
					$transaction->rollBack();

					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					Ajax::send(Ajax::AJAX_ERROR,
						Yii::t('GroupsModule.common',
							'При приглашении возникли ошибки, попробуйте пригласить пользователей позднее.'));
				}
			}
		}

		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.min.js'] = false;

		Ajax::send(Ajax::AJAX_SUCCESS,
			'ok',
			array(
			     'view' => $this->renderPartial('invite',
				     array(
				          //'model'      => $groupUser,
				          'groupUsers' => $groupUsers
				     ),
				     true,
				     true)
			));

	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer $id the ID of the model to be loaded
	 *
	 * @return Group the loaded model
	 * @throws CHttpException
	 */
	public function loadModel ( $id ) {
		$model = Group::model()->findByPk($id);
		if ( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param Group $model the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'group-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	protected function performTabularAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'group-form' ) {
			echo CActiveForm::validateTabular($model);
			Yii::app()->end();
		}
	}
}
