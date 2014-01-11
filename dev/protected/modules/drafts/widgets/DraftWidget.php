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
	 * Время разницы между локальным и серверным черновиком. Если локальный новее на это время, то будет загружен он.
	 * @var integer $timeOut
	 */
	public $timeDiff;

	public $draftMessage;

	public $notifyTime = 20000;

	public function init () {
		parent::init();

		if ( !$this->formId ) {
			throw new CException('Form id not set');
		}
		if ( !($this->model instanceof CActiveRecord) ) {
			throw new CException('Model must be instance of CActiveRecord');
		}
		if ( !$this->timeDiff ) {
			$this->timeDiff = 60;
		}

		if ( !$this->draftMessage ) {
			$this->draftMessage = Yii::t('draftsModule.common',
				'Найден черновик для этой формы. Видимо вы заполняли поля этой формы, но не сохранили их. Хотите загрузить черновик?{html1}Да, хочу{html2}{html3}Отмена{html4}',
				array(
					'{html1}' => '<br><br><a href="#" data-action="load-draft" class="btn btn-primary">',
					'{html2}' => '</a>',
					'{html3}' => '<a href="#" class="btn btn-link" data-action="remove-draft-notify">',
					'{html4}' => '</a>'
				));
		}

	}

	public function run () {
		$cs = Yii::app()->getClientScript();
		$cs->registerScript('appendDraft',
			'$("form#' . $this->formId . '").saveDraft(' . CJavaScript::encode(array(
				'createUrl'    => Yii::app()->createUrl('/drafts/default/create'),
				'getUrl'       => Yii::app()->createUrl('/drafts/default/get'),
				'deleteUrl'    => Yii::app()->createUrl('/drafts/default/delete'),
				'timeOut'      => (int) $this->timeDiff,
				'draftMessage' => $this->draftMessage,
				'notifyTime'   => $this->notifyTime,
			)) . '  );',
			CClientScript::POS_READY);
		$cs->registerScriptFile(Yii::app()->getModule('drafts')->getAssetsUrl() . '/js/drafts.js',
			CClientScript::POS_HEAD);

		Yii::app()->getUser()->setState('draft' . get_class($this->model), $this->formId);
	}
}