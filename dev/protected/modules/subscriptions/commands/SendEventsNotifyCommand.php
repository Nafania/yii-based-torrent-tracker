<?php
/**
 * Class SendEventsNotifyCommand
 * Команда для рассылки о непрочитанных уведомлениях
 * Принимает 1 аргумент - количество получателей в bcc
 */
class SendEventsNotifyCommand extends CConsoleCommand {
	public function run ( $args ) {
		$bccLimit = (isset($args[0]) ? (int) $args[0] : 500);

		Yii::import('ext.mail.*');

		$message = new YiiMailMessage;
		$message->view = 'application.modules.subscriptions.views.mail.notify';
		$message->setBody('', 'text/html');

		$message->subject = Yii::t('subscriptionsModule.common',
			'Новые уведомления на сайте {siteName}',
			array('{siteName}' => Yii::app()->config->get('base.siteName')));
		$message->from = Yii::app()->config->get('base.fromEmail');
		$message->to = Yii::app()->config->get('base.fromEmail');

		$sessionExpireTime = Yii::app()->getComponent('sessions')->timeout;
		$time = time() + $sessionExpireTime - 1 * 60 * 60; //время, которое пользователь отсутствует на сайте

		$db = Yii::app()->getDb();
		/*$sql = 'SELECT COUNT(*) AS count, u.id, u.email, u.name
		FROM {{events}} e, {{users}} u, {{sessions}} s
		WHERE e.uId = u.id
		AND u.emailConfirmed = 1
		AND e.unread = 1
		AND u.active = 1
		AND e.notified = 0
		AND u.id = s.uId
		AND s.expire < :time
		GROUP BY u.id';*/
		$sql = 'SELECT COUNT(*) AS count, u.id, u.email, u.name
		FROM {{events}} e, {{users}} u
		WHERE e.uId = u.id
		AND u.emailConfirmed = 1
		AND e.unread = 1
		AND u.active = 1
		AND e.notified = 0
		GROUP BY u.id';
		$comm = $db->createCommand($sql);
		$comm->bindValue(':time', $time);

		$dataReader = $comm->query();
		$bcc = array();
		$j = 0;
		foreach ( $dataReader AS $i => $row ) {
			$bcc[$j][$row['email']] = $row['name'];
			if ( $i >= $bccLimit ) {
				$j++;
			}
		}

		foreach ( $bcc AS $messages ) {
			$message->setBcc($messages);

			if ( !Yii::app()->mail->send($message) ) {
				throw new CHttpException(502, Yii::t('subscriptionsModule.common', 'Cant send mail'));
			}
		}

		//$db->createCommand('UPDATE {{events}} e, {{users}} u SET e.notified = 1 WHERE e.uId = u.id AND u.emailConfirmed = 1 AND e.unread = 1 AND u.active = 1 AND e.notified = 0')->execute();
		$db->createCommand('UPDATE {{events}} e, {{users}} u SET e.notified = 1 WHERE e.uId = u.id AND u.emailConfirmed = 1 AND e.unread = 1 AND u.active = 1 AND e.notified = 0')->execute();

		return 0;
	}
}