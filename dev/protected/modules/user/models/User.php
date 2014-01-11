<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer     $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property string      $resetHash
 * @property UserProfile $profile
 * @property integer     $emailConfirmed
 */
class User extends EActiveRecord {

	private $_identity;

	public $originalPassword;

	public $rememberMe;

	public $cacheTime = 3600;

	const FLASH_SUCCESS = 'success';
	const FLASH_NOTICE = 'notice';
	const FLASH_WARNING = 'warning';
	const FLASH_ERROR = 'error';
	const FLASH_INFO = 'info';

	const USER_ACTIVE = 1;
	const USER_NOT_ACTIVE = 0;


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return User the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	public function init () {
		parent::init();
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			/*
			 * login form
			 */
			array(
				'email, password',
				'required',
				'on' => 'login'
			),

			array(
				'password',
				'authenticate',
				'on' => 'login'
			),
			/*
			 * register form
			 */
			array(
				'email, password',
				'required',
				'on' => 'register'
			),
			array(
				'email',
				'unique',
				'on' => 'register'
			),

			/*
			* restore form
			*/
			array(
				'email',
				'required',
				'on' => 'restore'
			),
			array(
				'email',
				'exist',
				'attributeName' => 'email',
				'className'     => 'User',
				'on'            => 'restore'
			),

			/*
			* update form
			*/
			array(
				'email, name',
				'required',
				'on' => 'update'
			),
			array(
				'email',
				'unique',
				'on' => 'update'
			),
			/*
			 * socialLogin
			 */
			array(
				'name',
				'required',
				'on' => 'socialLogin'
			),
			array(
				'email',
				'unique',
				'on' => 'socialLogin'
			),
			/*
			* base rules
			*/
			array(
				'name, email, password',
				'length',
				'max' => 255
			),

			array(
				'rememberMe',
				'boolean'
			),

			array(
				'email',
				'email',
				'checkMX' => true,
			),
			/**
			 * имя должно быть не менее чем 2 символа и не более чем 50
			 */
			array(
				'name',
				'length',
				'min' => 2,
				'max' => 50,
			),
			/**
			 * регексп, для ограничения символов в имени
			 */
			array(
				'name',
				'match',
				'pattern' => '/^([а-яa-z0-9-_ ])+$/iu',
				'message' => Yii::t('userModule.common',
						'Вы можете использовать буквы, цифры, пробел, тире и подчеркивание.'),
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations () {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return CMap::mergeArray(parent::relations(),
			array(
				'profile'        => array(
					self::HAS_ONE,
					'UserProfile',
					'uid'
				),
				'socialAccounts' => array(
					self::HAS_MANY,
					'UserSocialAccount',
					'uId'
				),
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'               => 'ID',
			'name'             => Yii::t('userModule.common', 'Имя'),
			'email'            => Yii::t('userModule.common', 'Email адрес'),
			'password'         => Yii::t('userModule.common', 'Пароль'),
			'originalPassword' => Yii::t('userModule.common', 'Пароль'),
			'rememberMe'       => Yii::t('userModule.common', 'Запомнить меня'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search () {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('email', $this->email, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}


	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate ( $attribute, $params ) {

		if ( !$this->hasErrors() ) {
			$this->_identity = new UserIdentity($this->email, $this->password);
			if ( !$this->_identity->authenticate() ) {
				switch ( $this->_identity->errorCode ) {
					case UserIdentity::ERROR_EMAIL_INVALID:
						$this->addError('email',
							Yii::t('userModule.common', 'Указанный email не найден в базе данных'));
						break;
					case UserIdentity::ERROR_USER_NOT_ACTIVE:
						$this->addError('email', Yii::t('userModule.common', 'Этот аккаунт был отключен'));
						break;
					case UserIdentity::ERROR_PASSWORD_INVALID:
						$this->addError('password', Yii::t('userModule.common', 'Неверный пароль'));
						break;
					default:
						$this->addError('password', Yii::t('userModule.common', 'Неверный email адрес или пароль'));
						break;
				}
			}
		}
	}

	public function beforeSave () {
		switch ( $this->scenario ) {
			case 'register':
				$this->originalPassword = $this->password;
				$this->password = $this->hashPassword($this->password);
				break;

			case 'restore':
				$this->resetHash = md5(time() . uniqid());
				break;

			case 'reset':
				$this->originalPassword = $this->password = $this->generatePassword();
				$this->password = $this->hashPassword($this->password);
				$this->resetHash = '';
				break;

			case 'update':
				$old = User::model()->findByPk($this->getId());

				/**
				 * пароль не изменяется, удаляем его
				 */
				if ( $old->password === $this->password ) {
					unset($this->password);
				}

				if ( $this->password ) {
					$this->originalPassword = $this->password;
					$this->password = $this->hashPassword($this->password);
				}
				else {
					unset($this->password);
				}

				/**
				 * если меняется Email, то меняем статус на неподтвержденный
				 */
				if ( $old->getEmail() <> $this->getEmail() ) {
					$this->emailConfirmed = 0;
				}

				break;
		}

		if ( $this->getIsNewRecord() ) {
			$this->ctime = time();
			$this->active = self::USER_ACTIVE;

			if ( !$this->name ) {
				list($this->name) = explode('@', $this->email);
			}
		}

		return parent::beforeSave();
	}

	protected function afterSave () {
		parent::afterSave();

		switch ( $this->scenario ) {
			case 'register':
				$this->password = $this->originalPassword;
				$this->sendCreate();
				break;
		}
	}

	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			if ( $this->getId() != 1 && $this->getName() == 'admin' ) {
				$this->addError('name', 'There is can be only one admin! You shall not pass!');
				return false;
			}
			return true;
		}
		return false;
	}

	public function beforeDelete () {
		if ( parent::beforeDelete() ) {
			Yii::app()->getUser()->logout();

			return true;
		}
		return false;
	}


	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login () {
		if ( $this->_identity === null ) {
			$this->_identity = new UserIdentity($this->email, $this->password);
			$this->_identity->authenticate();
		}
		if ( $this->_identity->errorCode === UserIdentity::ERROR_NONE ) {
			$duration = ($this->rememberMe ? 30 * 24 * 60 * 60 : 0); // 30 days
			return Yii::app()->user->login($this->_identity, $duration);
		}
		else {
			return false;
		}
	}

	public function sendCreate () {
		Yii::import('ext.mail.*');

		$message = new YiiMailMessage;
		$message->view = 'application.modules.user.views.mail.create';
		$message->setBody(array('model' => $this), 'text/html');

		$message->subject = Yii::t('userModule.common',
			'Регистрация на сайте {siteName}',
			array('{siteName}' => Yii::app()->config->get('base.siteName')));
		$message->from = Yii::app()->config->get('base.fromEmail');
		$message->to = $this->email;

		if ( Yii::app()->mail->send($message) ) {
			return true;
		}
		else {
			throw new CHttpException(502, Yii::t('userModule.common', 'Cant send mail'));
		}
	}

	public function sendRestore () {
		Yii::import('ext.mail.*');

		$message = new YiiMailMessage;
		$message->view = 'application.modules.user.views.mail.restore';
		$message->setBody(array('model' => $this), 'text/html');

		$message->subject = Yii::t('userModule.common',
			'Восстановление пароля на сайте {siteName}',
			array('{siteName}' => Yii::app()->config->get('base.siteName')));
		$message->from = Yii::app()->config->get('base.fromEmail');
		$message->to = $this->email;

		if ( Yii::app()->mail->send($message) ) {
			return true;
		}
		else {
			throw new CHttpException(502, Yii::t('userModule.common', 'Cant send mail'));
		}
	}

	public function sendReset () {
		Yii::import('ext.mail.*');

		$message = new YiiMailMessage;
		$message->view = 'application.modules.user.views.mail.reset';
		$message->setBody(array('model' => $this), 'text/html');

		$message->subject = Yii::t('userModule.common',
			'Новый пароль на сайте {siteName}',
			array('{siteName}' => Yii::app()->config->get('base.siteName')));
		$message->from = Yii::app()->config->get('base.fromEmail');
		$message->to = $this->email;

		if ( Yii::app()->mail->send($message) ) {
			return true;
		}
		else {
			throw new CHttpException(502, Yii::t('userModule.common', 'Cant send mail'));
		}
	}

	public function sendConfirmEmail () {
		Yii::import('ext.mail.*');

		$code = md5(time() . $this->getId() . time());

		$message = new YiiMailMessage;
		$message->view = 'application.modules.user.views.mail.confirmEmail';
		$message->setBody(array(
				'model' => $this,
				'code'  => $code
			),
			'text/html');

		$message->subject = Yii::t('userModule.common',
			'Подтверждение вашего email адреса на {siteName}',
			array('{siteName}' => Yii::app()->config->get('base.siteName')));
		$message->from = Yii::app()->config->get('base.fromEmail');
		$message->to = $this->email;

		if ( Yii::app()->mail->send($message) ) {
			$UserConfirmCode = new UserConfirmCode();
			$UserConfirmCode->uId = $this->getId();
			$UserConfirmCode->confirmCode = $code;
			$UserConfirmCode->save(false);

			return true;
		}
		else {
			throw new CHttpException(502, Yii::t('userModule.common', 'Cant send mail'));
		}
	}

	/**
	 * Checks if the given password is correct.
	 *
	 * @param string the password to be validated
	 *
	 * @return boolean whether the password is valid
	 */
	public function validatePassword ( $password ) {
		if ( !$this->password ) {
			return false;
		}
		//if ($row['pass'] != md5($row['secret'] . $password . $row['secret'])) {
		if ( strpos($this->password, 'md5:') !== false ) {
			$linePos = strpos($this->password, '|');
			$pass = mb_substr($this->password, 4, $linePos - 4);
			$secret = mb_substr($this->password, $linePos + 1);
			//var_dump($pass, $secret);
			return $pass == md5($secret . $password . $secret);
		}
		return CPasswordHelper::verifyPassword($password, $this->password);
	}

	/**
	 * Generates the password hash.
	 *
	 * @param string password
	 *
	 * @return string hash
	 */
	public function hashPassword ( $password ) {
		return CPasswordHelper::hashPassword($password);
	}

	public function generatePassword ( $length = 8 ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$pass = substr(str_shuffle($chars), 0, $length);

		return $pass;
	}

	public function getId () {
		return $this->id;
	}

	public function getName () {
		return $this->name;
	}

	public function getEmail () {
		return $this->email;
	}

	public function getUrl () {
		return array(
			'/user/default/view',
			'id'   => $this->getId(),
			'name' => $this->getName(),
		);
	}

	public function getCtime ( $format = false ) {
		if ( $format ) {
			return date($format, $this->ctime);
		}
		return $this->ctime;
	}
}