<?php
class StaticpagesModule extends CWebModule {

	public $backendController = 'staticpagesBackend';
	public $defaultController = 'default';

	public function init () {
		$this->setImport(array(
		                      'application.modules.staticpages.models.StaticPage'
		                 ));
	}

	public static function register () {
		self::_addUrlRules();

		Yii::app()->pd->addAdminModule('staticpages', 'static pages management');
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/staticpages/backend/<action:\w+>/*' => 'staticpages/staticpagesBackend/<action>',
		                                 'yiiadmin/staticpages/backend/*'              => 'staticpages/staticpagesBackend',

		                                 'page/<view>'                         => '/staticpages/default/index',
		                            ),
			false);
	}

	public function getPublishedPagesAsMenu () {
		$models = StaticPage::model()->published()->findAll();
		$items = array();
		foreach ( $models AS $model ) {
			$items[] = array(
				'label' => $model->getTitle(),
				'url' => $model->getUrl(),
			);
		}

		return $items;
	}

	public function install () {
		Yii::import('application.modules.staticpages.migrations.Install');

		$Install = new Install();
		$Install->safeUp();
	}

	public function uninstall () {
		Yii::import('application.modules.staticpages.migrations.Install');

		$Install = new Install();
		$Install->safeDown();
	}
}