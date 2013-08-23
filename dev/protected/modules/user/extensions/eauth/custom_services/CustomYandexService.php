<?php
/**
 * An example of extending the provider class.
 *
 * @author  Maxim Zemskov <nodge@yandex.ru>
 * @link    http://code.google.com/p/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)) . '/services/YandexOpenIDService.php';

class CustomYandexService extends YandexOpenIDService {

	protected $jsArguments = array(
		'popup' => array(
			'width'  => 900,
			'height' => 620
		)
	);

	protected $requiredAttributes = array(
		/*'name'  => array(
			'nickname',
			'namePerson/friendly'
		),
		'firstname' => array(
			'firstname',
			'namePerson/first'
		),
		'lastname' => array(
			'lastname',
			'namePerson/last'
		),*/
		'email'     => array(
			'email',
			'contact/email'
		),
	);

	protected function fetchAttributes () {
		if ( isset($this->attributes['username']) && !empty($this->attributes['username']) ) {
			$this->attributes['url'] = 'http://openid.yandex.ru/' . $this->attributes['username'];
		}

		if ( empty($this->attributes['name']) && ( isset($this->attributes['firstname']) && isset($this->attributes['lastname'])) ) {
			$this->attributes['name'] = $this->attributes['firstname'] . ' ' . $this->attributes['lastname'];
		}
		list($this->attributes['name']) = explode('@', $this->attributes['email']);
	}
}