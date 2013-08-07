<?php

class DefaultController extends Controller {

	/**
	 * Displays the login page
	 */
	public function actionLogin () {
		$this->pageTitle = Yii::t('userModule.common', 'Вход');
		$this->breadcrumbs[] = Yii::t('userModule.common', 'Вход');

		$User = new User('login');

		$this->performAjaxValidation($User);

		// collect user input data
		if ( isset($_POST['User']) ) {
			$User->attributes = $_POST['User'];
			// validate user input and redirect to the previous page if valid
			if ( $User->validate() && $User->login() ) {
				Yii::app()->user->setFlash(User::FLASH_SUCCESS, Yii::t('userModule.common', 'Login successful'));
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		// display the login form
		$this->render('login', array('model' => $User));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout () {
		Yii::app()->user->setFlash(User::FLASH_SUCCESS, Yii::t('userModule.common', 'Logout successful'));
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionRegister () {
		$this->pageTitle = Yii::t('userModule.common', 'Регистрация');
		$this->breadcrumbs[] = Yii::t('userModule.common', 'Регистрация');

		$User = new User('register');
		$Profile = new UserProfile('register');

		$this->performAjaxValidation($User);

		if ( isset($_POST['User']) ) {
			$User->attributes = $_POST['User'];
			if ( $User->save() ) {

				$Profile->uid = $User->getId();
				$Profile->save(false);

				Yii::app()->getAuthManager()->assign('registered', $User->getId());

				Yii::app()->user->setFlash(User::FLASH_SUCCESS, Yii::t('userModule.common', 'Register successful'));

				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		$this->render('register', array('model' => $User));
	}

	public function actionRestore () {
		$this->pageTitle = Yii::t('userModule.common', 'Восстановление пароля');
		$this->breadcrumbs[] = Yii::t('userModule.common', 'Восстановление пароля');

		$User = new User('restore');

		$this->performAjaxValidation($User);

		if ( isset($_POST['User']) ) {
			$User->attributes = $_POST['User'];
			if ( $User->validate() ) {
				$User = User::model()->findByAttributes(array('email' => $User->email));
				$User->setScenario('restore');

				if ( $User->save() && $User->sendRestore() ) {
					Yii::app()->user->setFlash(User::FLASH_SUCCESS, Yii::t('userModule.common', 'Restore successful. Email with future instructions sent to you.'));

					$this->redirect(Yii::app()->user->returnUrl);
				}
			}
		}
		$this->render('restore', array('model' => $User));
	}

	public function actionReset ( $hash ) {
		$this->pageTitle = Yii::t('userModule.common', 'Сброс пароля');
		$this->breadcrumbs[] = Yii::t('userModule.common', 'Сброс пароля');

		$User = User::model()->findByAttributes(array('resetHash' => $hash));

		if ( !$User ) {
			throw new CHttpException(404, Yii::t('userModule.common', 'User not found'));
		}

		$User->setScenario('reset');

		if ( $User->save() && $User->sendReset() ) {
			Yii::app()->user->setFlash(User::FLASH_SUCCESS, Yii::t('userModule.common', 'Reset successful'));
			$this->redirect(Yii::app()->homeUrl);
		}

	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel ( $id ) {
		$model = User::model()->findByPk((int) $id);
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
		if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}