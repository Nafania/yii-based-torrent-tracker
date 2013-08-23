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
				'email'
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
						$this->addError('email', Yii::t('userModule.common', 'Email not found in database'));
						break;
					case UserIdentity::ERROR_USER_NOT_ACTIVE:
						$this->addError('email', Yii::t('userModule.common', 'User account deactivated'));
						break;
					case UserIdentity::ERROR_PASSWORD_INVALID:
						$this->addError('password', Yii::t('userModule.common', 'Incorrect password.'));
						break;
					default:
						$this->addError('password', Yii::t('userModule.common', 'Incorrect email or password.'));
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
				if ( $this->password ) {
					$this->originalPassword = $this->password;
					$this->password = $this->hashPassword($this->password);
				}
				else {
					unset($this->password);
				}

				$old = User::model()->findByPk($this->getId());

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
			$duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
			Yii::app()->user->login($this->_identity, $duration);
			return true;
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
			'id' => $this->getId()
		);
	}
}