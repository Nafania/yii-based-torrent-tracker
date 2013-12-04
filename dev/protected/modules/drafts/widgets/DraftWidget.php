<?php
class DraftWidget extends CWidget {
	/**
	 * @var string $formId
	 */
	public $formId;

	/**
	 * @var CActiveRecord $model
	 */
	public $model;

	/**
	 * Время актуальности черновика
	 * @var integer $timeOut
	 */
	public $timeOut;

	public function init () {
		parent::init();

		if ( !$this->formId ) {
			throw new CException('Form id not set');
		}
		if ( !($this->model instanceof CActiveRecord) ) {
			throw new CException('Model must be instance of CActiveRecord');
		}
		if ( !$this->timeOut ) {
			$this->timeOut = 7 * 24 * 60 * 60;
		}

	}

	public function run () {
		$cs = Yii::app()->getClientScript();
		$cs->registerScript('appendDraft',
			'$("form#' . $this->formId . '").saveDraft(' . CJavaScript::encode(array(
			                                                                         'createUrl' => Yii::app()->createUrl('/drafts/default/create'),
			                                                                         'getUrl'    => Yii::app()->createUrl('/drafts/default/get'),
			                                                                         'deleteUrl' => Yii::app()->createUrl('/drafts/default/delete'),
			                                                                         'timeOut'   => (int) $this->timeOut,
			                                                                    )) . '  );',
			CClientScript::POS_READY);
		$cs->registerScriptFile(Yii::app()->getModule('drafts')->getAssetsUrl() . '/js/drafts.js',
			CClientScript::POS_HEAD);

		Yii::app()->getUser()->setState('draft' . get_class($this->model), $this->formId);
	}
}