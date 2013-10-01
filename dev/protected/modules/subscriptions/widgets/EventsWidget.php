<?php
class EventsWidget extends CWidget {
	public function run () {
		$host = Yii::app()->config->get('subscriptionsModule.socketIOHost');
		$host = ( $host ? $host : Yii::app()->getRequest()->getBaseUrl(true));

		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile($host . ':' . Yii::app()->config->get('subscriptionsModule.socketIOPort') . '/socket.io/socket.io.js');

		$config = array(
			'host' => $host,
			'port' => Yii::app()->config->get('subscriptionsModule.socketIOPort'),
			'hash' => md5(Yii::app()->getUser()->getId()),
		);
		Yii::app()->clientScript->registerScript('eventsConfig',
			'var eventsConfig=' . CJavaScript::encode($config) . ';',
			CClientScript::POS_END);

		$eventItemsCount = Event::model()->unreaded()->forCurrentUser()->count();

		$this->render('eventsWidget', array(
		                                   'eventItemsCount' => $eventItemsCount,
		                              ));
	}
}