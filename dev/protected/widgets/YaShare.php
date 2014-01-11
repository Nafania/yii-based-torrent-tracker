<?php
class YaShare extends CWidget {
	public $type = false;
	public $theme = 'counter';
	public $services = 'yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,gplus';
	public $lang;

	public function run() {
		if ( !$this->lang ) {
			$this->lang = Yii::app()->getRequest()->getPreferredLanguage();
		}

		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile('//yandex.st/share/share.js', CClientScript::POS_END);

		echo '<div' . ( $this->theme ? '  data-yashareTheme="' . $this->theme . '"' : '' ) . '' . ( $this->type ? ' data-yashareType="' . $this->type . '"' : '' ) . ' class="yashare-auto-init" data-yashareL10n="' . $this->lang . '" data-yashareQuickServices="' . $this->services . '"></div>';
	}
}