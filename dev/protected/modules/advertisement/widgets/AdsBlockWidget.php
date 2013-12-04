<?php
class AdsBlockWidget extends CWidget {
	public $systemName;
	public $model;

	public function run () {
		$ads = Advertisement::model()->findByAttributes(array('systemName' => $this->systemName));

		if ( !$ads ) {
			return;
		}

		if ( $ads->bizRule && !@eval($ads->bizRule) ) {
			return;
		}

		$code = $ads->code;

		preg_match_all('/<script(.*?)>(.*?)<\/script>/si', $code, $matches);

		if ( $matches ) {
			$cs = Yii::app()->getClientScript();
			foreach ( $matches[1] AS $i => $src ) {
				if ( strpos($src, 'src') !== false ) {
					preg_match('/src=["\'](.*?)["\']/', $src, $_matches);
					$cs->registerScriptFile($_matches[1], CClientScript::POS_END);
				}
				else {
					$cs->registerScript('ads' . $i, $matches[2][$i], CClientScript::POS_READY);
				}
			}

			$code = preg_replace('/<script(.*?)>(.*?)<\/script>/is', '', $code);
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