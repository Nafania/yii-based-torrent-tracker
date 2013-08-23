<?php

class DefaultController extends Controller {

	public function filters () {
		return array(
			array('application.modules.auth.filters.AuthFilter -logout,socialLogin,login'),
		);
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin () {
		Yii::import('application.modules.user.models.*');

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

				Yii::app()->user->setFlash(User::FLASH_SUCCESS,
					Yii::t('userModule.common', 'Register and login successful'));

				$User->login();

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
					Yii::app()->user->setFlash(User::FLASH_SUCCESS,
						Yii::t('userModule.common', 'Restore successful. Email with future instructions sent to you.'));

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
			Yii::app()->user->setFlash(User::FLASH_SUCCESS,
				Yii::t('userModule.common',
					'Reset successfull. Email with future instructions sent to you. Please, follow link from that email.'));
			$this->redirect(Yii::app()->homeUrl);
		}

	}

	public function actionConfirmEmail ( $c = '' ) {
		$User = User::model()->findByPk(Yii::app()->getUser()->getId());

		if ( $User->emailConfirmed ) {
			Yii::app()->user->setFlash(User::FLASH_INFO, Yii::t('userModule.common', 'Your email already confirmed'));
			$this->redirect(array('/user/default/settings'));
		}

		if ( $c ) {
			$ConfirmCode = UserConfirmCode::model()->findByAttributes(array('uId' => Yii::app()->getUser()->getId()));
			UserConfirmCode::model()->deleteAllByAttributes(array('uId' => Yii::app()->getUser()->getId()));
			if ( $ConfirmCode && $ConfirmCode->confirmCode == $c ) {
				$User->emailConfirmed = 1;
				$User->save();

				Yii::app()->user->setFlash(User::FLASH_SUCCESS,
					Yii::t('userModule.common', 'You successfully confirm your email address'));
				$this->redirect(Yii::app()->homeUrl);
			}
			else {
				Yii::app()->user->setFlash(User::FLASH_ERROR,
					Yii::t('userModule.common', 'Incorrect confirm code, please repeat confirm email procedure.'));
				$this->redirect(array('/user/default/settings'));
			}
		}
		else {
			$User->sendConfirmEmail();
			Yii::app()->user->setFlash(User::FLASH_SUCCESS,
				Yii::t('userModule.common',
					'Confirm mail sent to you. Please, follow link from mail to confirm your email address.'));
			$this->redirect(array('/user/default/settings'));
		}
	}

	public function actionSettings () {
		$this->pageTitle = Yii::t('userModule.common', 'Настройки');
		$this->breadcrumbs[] = Yii::t('userModule.common', 'Настройки');

		$User = User::model()->findByPk(Yii::app()->getUser()->getId());

		if ( !$User ) {
			throw new CHttpException(404);
		}
		$Profile = $User->profile;

		$component = Yii::app()->eauth;
		$services = $component->getServices();

		if ( isset($_POST['User']) ) {
			$User->attributes = $_POST['User'];
			$Profile->attributes = $_POST['Profile'];

			$valid = $User->validate();
			$valid = $Profile->validate() && $valid;

			if ( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$User->save(false);
					$Profile->save(false);

					$transaction->commit();

					Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
						Yii::t('userModule.common', 'Профиль успешно сохранен'));
					$this->redirect(array('/user/default/settings'));
				} catch ( Exception $e ) {

					$transaction->rollBack();
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

					Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
						Yii::t('userModule.common',
							'При сохранении профиля возникли проблемы, пожалуйста, попробуйте позже.'));
				}
			}
		}

		$this->render('settings',
			array(
			     'user'    => $User,
			     'profile' => $Profile,
			     'socialServices' => $services
			));
	}

	public function actionSocialAdd ( $service ) {
		$authIdentity = Yii::app()->eauth->getIdentity($service);
		$authIdentity->redirectUrl = $this->createAbsoluteUrl('/user/default/settings');
		$authIdentity->cancelUrl = $this->createAbsoluteUrl('/user/default/settings');

		try {
			if ( $authIdentity->authenticate() ) {
				$identity = new EAuthUserIdentity($authIdentity);

				if ( $identity->authenticate() ) {
					$UserSocialAccount = new UserSocialAccount();
					$UserSocialAccount->service = $service;
					$UserSocialAccount->id = $identity->id;
					$UserSocialAccount->uId = Yii::app()->getUser()->getId();
					$UserSocialAccount->name = $authIdentity->name;

					if ( $UserSocialAccount->save() ) {
						Yii::app()->getUser()->setFlash(User::FLASH_SUCCESS,
							Yii::t('userModule.common',
								'Аккаунт социальной сети {title} успешно добавлен, теперь вы можете входить на сайт, используя этот аккаунт.',
								array('{title}' => $service)));
						$authIdentity->redirect();
					}
					else {
						Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
							Yii::t('userModule.common',
								'При добавлении аккаунта возникли ошибки: {errors}',
								array('{errors}' => $UserSocialAccount->getError('id'))));
						$authIdentity->redirect();
					}
				}
			}
		} catch ( CException $e ) {
			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
		}
		Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
			Yii::t('userModule.common',
				'При добавлении аккаунта возникли ошибки, пожалуйста, попробуйте добавить аккаунт позднее.'));
		$authIdentity->redirect();
	}

	public function actionSocialLogin ( $service ) {
		Yii::import('application.modules.user.models.*');

		$this->pageTitle = Yii::t('userModule.common', 'Вход через социальные сети');
		$this->breadcrumbs[] = Yii::t('userModule.common', 'Вход через социальные сети');

		$User = new User('socialLogin');
		$Profile = new UserProfile('socialLogin');

		$authIdentity = Yii::app()->eauth->getIdentity($service);
		$authIdentity->redirectUrl = Yii::app()->user->returnUrl;
		$authIdentity->cancelUrl = $this->createAbsoluteUrl('/user/default/login');

		try {
			$transaction = Yii::app()->db->beginTransaction();

			if ( $authIdentity->authenticate() ) {
				$identity = new EAuthUserIdentity($authIdentity);

				if ( $identity->authenticate() ) {

					$UserSocialAccount = $identity->getSocialAccount();

					if ( !$UserSocialAccount ) {
						$UserSocialAccount = new UserSocialAccount();

						$userAttributes = array(
							'name'  => $authIdentity->name,
							'email' => $authIdentity->email,
						);
						$User->emailConfirmed = ($authIdentity->email ? 1 : 0);

						$profileAttributes = array(
							'picture' => (isset($authIdentity->avatar) ? $authIdentity->avatar : ''),
						);
						$User->attributes = $userAttributes;
						$Profile->attributes = $profileAttributes;

						$UserSocialAccount->service = $service;
						$UserSocialAccount->id = $identity->id;
						$UserSocialAccount->name = $authIdentity->name;

						if ( $User->validate() ) {
							$User->save(false);

							$Profile->uid = $User->getId();
							$Profile->save(false);

							$UserSocialAccount->uId = $User->getId();
							$UserSocialAccount->save(false);

							Yii::app()->getAuthManager()->assign('registered', $User->getId());

							$identity->id = $User->getId();

							$transaction->commit();

							Yii::app()->user->setFlash(User::FLASH_SUCCESS,
								Yii::t('userModule.common', 'Login successful'));
							Yii::app()->user->login($identity);
							$authIdentity->redirect();
						}
						else {
							Yii::log(var_export($User->getErrors(), true), CLogger::LEVEL_ERROR);
							Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
								Yii::t('userModule.common',
									'При входе через социальную сеть {socialTitle} возникла ошибка. Пожалуйста, попробуйте зайти позже или выберете другую социальную сеть.',
									array('{socialTitle}' => $service)));
							$authIdentity->redirect($authIdentity->getCancelUrl());
						}
					}
					else {
						$identity->id = $UserSocialAccount->uId;

						Yii::app()->user->setFlash(User::FLASH_SUCCESS,
							Yii::t('userModule.common', 'Login successful'));
						Yii::app()->user->login($identity);
						$authIdentity->redirect();
					}
				}
				else {
					$authIdentity->cancel();
				}
			}
			else {
				$this->redirect(array('/user/default/login'));
			}
		} catch ( CException $e ) {
			$transaction->rollBack();

			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
			Yii::app()->getUser()->setFlash(User::FLASH_ERROR,
				Yii::t('userModule.common',
					'При входе через социальную сеть {socialTitle} возникла ошибка. Пожалуйста, попробуйте зайти позже или выберете другую социальную сеть.',
					array('{socialTitle}' => $service)));

			$authIdentity->redirect($authIdentity->getCancelUrl());
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