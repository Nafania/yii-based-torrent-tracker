<?php
class AdsBlockWidget extends CWidget {
	public $systemName;
	public $model;

	public function run () {
		$ads = Advertisement::model()->findByAttributes(array('systemName' => $this->systemName));

		if ( !$ads ) {
			return;
		}

		$code = $ads->code;
		preg_match_all('/script(.*?) src=("|\')(.*?)("|\')/', $code, $matches);
		if ( $matches ) {
			$cs = Yii::app()->getClientScript();
			$code = preg_replace('/<script(.*?)>(.*?)<\/script>/i', '', $code);
			foreach ( $matches[3] AS $script ) {
				$cs->registerScriptFile($script, CClientScript::POS_END);
			}
		}
		if ( strpos($code, '<?php') === 0 ) {
			$code = str_replace('$model', '$this->model', $code);
			$code = str_replace('<?php', '', $code);
			eval($code);
		}
		else {
			echo $code;
		}
	}
}