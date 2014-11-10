<?php
class ReportModal extends CWidget {

	public function run () {
		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('reports')->getAssetsUrl() . '/js/reports.js');

		Yii::import('application.modules.reports.models.*');

		$this->render('reportModal');
	}
}