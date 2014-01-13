<?php

class UserModule extends CWebModule {
	public $backendController = 'usersBackend';
	public $defaultController = 'default';

	private $_assetsUrl;

	public function init () {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'user.models.*',
			'user.components.*',
		));

	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null ) {
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.user.assets'));
		}
		return $this->_assetsUrl;
	}

	public static function register () {
		self::_registerComponent();
		self::_addUrlRules();
		self::_setImport();

		Yii::app()->pd->addAdminModule('user', 'Models management');
	}

	private static function _registerComponent () {
		$services = @unserialize(Yii::app()->config->get('userModule.socialServices'));
		$services = (is_array($services) ? $services : array());

		Yii::app()->pd->registerApplicationComponents(array(
			'user'  => array(
				'class'           => 'application.modules.user.components.WebUser',
				'allowAutoLogin'  => true,
				'autoRenewCookie' => true,
				'loginUrl'        => array('/user/default/login'),
				'registerUrl'     => array('/user/default/register'),
				//'loginRequiredAjaxResponse' => 'logged-out',
				'autoUpdateFlash' => false,
				// add this line to disable the flash counter
				'admins'          => array('admin'),
				// users with full access

			),
			'loid'  => array(
				'class' => 'application.modules.user.extensions.lightopenid.loid',
			),
			'eauth' => array(
				'class'    => 'application.modules.user.extensions.eauth.EAuth',
				'popup'    => true,
				// Использовать всплывающее окно вместо перенаправления на сайт провайдера
				'services' => $services
			),
		));
	}

	protected static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
			'yiiadmin/user/backend/<action:\w+>/*' => 'user/usersBackend/<action>',
			'yiiadmin/user/backend/*'              => 'user/usersBackend',

			'user/<name>-<id>'                     => 'user/default/view',
			'user/<action:\w+>/*'                  => 'user/default/<action>',
			'user/<controller:\w+>/<action:\w+>/*' => 'user/<controller>/<action>',
		));
	}

	private static function _setImport () {

		Yii::app()->pd->setImport(array(
			'application.modules.user.extensions.eoauth.*',
			'application.modules.user.extensions.eoauth.lib.*',
			'application.modules.user.extensions.lightopenid.*',
			'application.modules.user.extensions.eauth.*',
			'application.modules.user.extensions.eauth.services.*',
			'application.modules.user.extensions.eauth.custom_services.*',
		));
	}
}
