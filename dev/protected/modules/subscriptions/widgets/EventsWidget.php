<?php

class EventsWidget extends CWidget {
	public $cookieName = 'readedEvents';

	public function run () {
		$this->markEventReaded();

		$host = Yii::app()->config->get('subscriptionsModule.socketIOHost');
		$host = ($host ? $host : Yii::app()->getRequest()->getBaseUrl(true));
		$port = Yii::app()->config->get('subscriptionsModule.socketIOPort');

		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('cookie');
		$cs->registerScriptFile(Yii::app()->getModule('subscriptions')->getAssetsUrl() . '/js/events.js');
		$cs->registerScript('eventRead',
			"$(document).on('click', 'a[data-action=event]', function (e) {
				var elem = $(this)
				, href = elem.attr('href')
				, readedEvents = JSON.parse($.cookie(" . CJavaScript::encode($this->cookieName) . "));

				if ( !readedEvents ) {
					readedEvents = [];
				}
				readedEvents.push(elem.data('id'));
				$.cookie(" . CJavaScript::encode($this->cookieName) . ", JSON.stringify(readedEvents), {path: '/'});

				var currentUrl = window.location.href.toString().split(window.location.host)[1].replace(/#.*$/, '');
                if ( href.indexOf('#') != -1 && currentUrl == href.replace(/#.*$/, '') ) {
                    window.location.href = href;
                    window.location.reload(true);
                }
		});");

		if ( $port ) {
			$cs->registerScriptFile($host . ':' . $port . '/socket.io/socket.io.js');

			$config = array(
				'host' => $host,
				'port' => $port,
				'hash' => md5(Yii::app()->getUser()->getId()),
			);
			Yii::app()->clientScript->registerScript('eventsConfig',
				'var eventsConfig=' . CJavaScript::encode($config) . ';',
				CClientScript::POS_END);
		}

		$eventItemsCount = Event::model()->unreaded()->forCurrentUser()->count();

		$this->render('eventsWidget',
			array(
				'eventItemsCount' => $eventItemsCount,
			));
	}

	public function markEventReaded () {
		$cookies = Yii::app()->getRequest()->cookies[$this->cookieName];
		$ids = CJavaScript::jsonDecode($cookies);

		if ( $ids ) {
			$events = Event::model()->findAllByPk($ids);
			foreach ( $events AS $event ) {
				$event->unread = Event::EVENT_READED;
				$event->save();
			}

			if ( $cookies ) {
				Yii::app()->getRequest()->cookies[$this->cookieName] = new CHttpCookie($this->cookieName, null, array(
					'expire' => time() - 1 * 60 * 60,
				));
				unset(Yii::app()->getRequest()->cookies[$this->cookieName]);
			}
		}
	}
}