<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {
	private $_id;

	public $email;

	const ERROR_EMAIL_INVALID = 3;
	const ERROR_USER_NOT_ACTIVE = 4;

	public function __construct ( $email, $password ) {
		$this->email = $email;
		$this->password = $password;
	}

	public function authenticate () {
		$record = User::model()->findByAttributes(array('email'=> $this->email));
		if ( $record === null ) {
			$this->errorCode = self::ERROR_EMAIL_INVALID;
		}
		else if ( !$record->validatePassword($this->password) ) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}
		elseif ( $record->active == User::USER_NOT_ACTIVE ) {
			$this->errorCode = self::ERROR_USER_NOT_ACTIVE;
		}
		else {
			$this->_id = $record->id;
			$this->errorCode = self::ERROR_NONE;
		}

		return !$this->errorCode;
	}

	public function getId () {
		return $this->_id;
	}
}