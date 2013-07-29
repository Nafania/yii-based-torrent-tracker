<?php
class UserMenu extends CWidget {
	public function run () {
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