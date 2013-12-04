<?php
/*
 * Mjm Chat Extension
 */

/**
 * Description of Mjm Chat
 * @author  Mohammad Javad Masoudian (MJM) <2007mjm@gmail.com>
 * @version 1.0
 */

class MjmChat extends CWidget {
	public function init () {
	}

	public function run () {
		$host = Yii::app()->config->get('chatModule.socketIOHost');
		$host = ( $host ? $host : Yii::app()->getRequest()->getBaseUrl(true));
		$port = Yii::app()->config->get('chatModule.socketIOPort');

		if ( Yii::app()->getUser()->getIsGuest() || !$port ) {
			return;
		}
		
		// Register JS script
		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('cookie');
		$cs->registerScriptFile($host . ':' . $port . '/socket.io/socket.io.js');

		$profileUrl = Yii::app()->getUser()->getUrl();

		$config = array(
			'host' => $host,
			'port' => $port,
			'user' => array(
				'name' => Yii::app()->getUser()->getName(),
				'url' => Yii::app()->createUrl(array_shift($profileUrl), $profileUrl),
				'id' => Yii::app()->getUser()->getId(),
				'avatar' => Yii::app()->getUser()->profile->getImageUrl(32,32),
				'rating' => Yii::app()->getUser()->getRating(),
				'ratingClass' => Yii::app()->getUser()->getRatingClass(),
			)
		);
		$cs->registerScript('mjmChatConfig',
			'var mjmChatConfig=' . CJavaScript::encode($config) . ';',
			CClientScript::POS_END);

		$cs->registerScriptFile(Yii::app()->getModule('chat')->getAssetsUrl() . '/javascript/chatSocket.js');
		$cs->registerScriptFile(Yii::app()->getModule('chat')->getAssetsUrl() . '/javascript/moment/moment.js');
		//TODO: load actual language
		$cs->registerScriptFile(Yii::app()->getModule('chat')->getAssetsUrl() . '/javascript/moment/lang/ru.js');
		$cs->registerScriptFile(Yii::app()->getModule('chat')->getAssetsUrl() . '/javascript/livestamp.min.js');
		$cs->registerCssFile(Yii::app()->getModule('chat')->getAssetsUrl() . '/css/mjmChat.css');

		$showChat = Yii::app()->getRequest()->cookies['showChat']->value;

		// Render view
		$this->render('mjmChatView', array(
		                                  'showChat' => ( $showChat ? true : false ),
		                             ));
	}
}