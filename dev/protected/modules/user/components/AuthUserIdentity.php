<?php
class AuthUserIdentity extends EAuthUserIdentity {
	public function authenticate() {
		if ($this->service->isAuthenticated) {
			$this->id = $this->service->id;
			$this->name = $this->service->getAttribute('name');

			/*$this->setState('id', $this->id);
			$this->setState('name', $this->name);
			$this->setState('service', $this->service->serviceName);*/

			// You can save all given attributes in session.
			//$attributes = $this->service->getAttributes();
			//$session = Yii::app()->session;
			//$session['eauth_attributes'][$this->service->serviceName] = $attributes;

			$this->errorCode = self::ERROR_NONE;
		}
		else {
			$this->errorCode = self::ERROR_NOT_AUTHENTICATED;
		}
		return !$this->errorCode;
	}

	public function getSocialAccount () {
		if ( $this->id !== null ) {
            $account = UserSocialAccount::model()->findByAttributes(array(
                'id' => $this->id,
                'service' => $this->service->getServiceName()
            ));

			if ( $account ) {
				return $account;
			}
		}
		return false;
	}

	public function setId ( $value ) {
		$this->id = $value;
	}
}