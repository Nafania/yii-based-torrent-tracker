<?php

Yii::import('application.modules.auth.components.*');
Yii::import('application.modules.user.models.*');

class WebUser extends AuthWebUser {

	// Store model to not repeat query.
	private $model;

	public $loginRequiredAjaxResponse;

	public $registerUrl;

	public function init () {
		parent::init();
		$this->attachBehaviors($this->behaviors());
	}

	public function behaviors () {
		return Yii::app()->pd->loadBehaviors($this);
	}

	public function getModel () {
		if ( !isset($this->id) ) {
			$this->model = new User;
		}
		if ( $this->model === null ) {
			$this->model = User::model()->findByPk($this->id);

			if ( !$this->getIsGuest() ) {

				if ( !$this->model->emailConfirmed ) {
					$this->setFlash(User::FLASH_WARNING,
						Yii::t('userModule.common',
							'Ваш email не подтвержден. Пожалуйста, подтвердите его на странице <a href="{url}">настроек</a> вашего аккаунта, иначе вы не сможете пользоваться всеми функциями сайта.',
							array('{url}' => Yii::app()->createUrl('/user/default/settings'))));
				}

				if ( strpos($this->model->password, 'md5:') !== false ) {
					$this->setFlash(User::FLASH_WARNING,
						Yii::t('userModule.common',
							'Вам необходимо сменить пароль для обеспечения безопасности своего аккаунта. Пожалуйста, смените его на странице <a href="{url}">настроек</a> вашего аккаунта и это сообщение исчезнет.',
							array('{url}' => Yii::app()->createUrl('/user/default/settings'))));
				}
			}

		}

		return $this->model;
	}

	public function __get ( $name ) {
		try {
			return parent::__get($name);
		} catch ( CException $e ) {
			$m = $this->getModel();
			if ( $m->__isset($name) ) {
				return $m->{$name};
			}
			else {
				throw $e;
			}
		}
	}

	public function __set ( $name, $value ) {
		try {
			return parent::__set($name, $value);
		} catch ( CException $e ) {
			$m = $this->getModel();
			$m->{$name} = $value;
		}
	}

	public function __call ( $name, $parameters ) {
		try {
			return parent::__call($name, $parameters);
		} catch ( CException $e ) {
			$m = $this->getModel();
			return call_user_func_array(array(
			                                 $m,
			                                 $name
			                            ),
				$parameters);
		}
	}

	public function getName () {
		return $this->getModel()->getName();
	}

	public function loginRequired () {
		if ( Yii::app()->request->getIsAjaxRequest() ) {
			$this->_sendLoginRequired();

			Yii::app()->end();
		}
		else {
			Yii::app()->user->setFlash(User::FLASH_ERROR,
				Yii::t('userModule.common', 'Для выполнения данного действия вам необходимо войти на сайт.'));
			parent::loginRequired();
		}
	}

	private function _sendLoginRequired () {
		Ajax::send(Ajax::AJAX_ERROR,
			Yii::t('userModule.common',
				'Для выполнения данного действия вам необходимо войти на сайт. Кликните <a href="{url}">здесь</a>, чтобы войти на сайт.',
				array(
				     '{url}' => Yii::app()->createUrl('/user/default/login')
				)));
	}

	public function setState ( $key, $value, $defaultValue = null ) {
		$key = $this->getStateKeyPrefix() . $key;
		if ( $value === $defaultValue ) {
			unset(Yii::app()->session[$key]);
		}
		else {
			Yii::app()->session[$key] = $value;
		}
	}

	public function getState ( $key, $defaultValue = null ) {
		$key = $this->getStateKeyPrefix() . $key;
		return isset(Yii::app()->session[$key]) ? Yii::app()->session[$key] : $defaultValue;
	}

	public function hasState ( $key ) {
		$key = $this->getStateKeyPrefix() . $key;
		return isset(Yii::app()->session[$key]);
	}

	public function clearStates () {
		$keys = array_keys(Yii::app()->session);
		$prefix = $this->getStateKeyPrefix();
		$n = strlen($prefix);
		foreach ( $keys as $key ) {
			if ( !strncmp($key, $prefix, $n) ) {
				unset(Yii::app()->session[$key]);
			}
		}
	}
}