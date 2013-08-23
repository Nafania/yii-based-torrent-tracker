<?php
class UserMenu extends CWidget {
	public function run () {
		Yii::import('application.modules.user.models.*');
		$loginModel = new User('login');

		$registerModel = new User('register');

		$restoreModel = new User('restore');

		$this->render('userMenu',
			array(
			     'loginModel' => $loginModel,
			     'registerModel' => $registerModel,
			     'restoreModel' => $restoreModel,
			));
	}
}