<?php
/**
 * An example of extending the provider class.
 *
 * @author  Maxim Zemskov <nodge@yandex.ru>
 * @link    http://code.google.com/p/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)) . '/services/GoogleOpenIDService.php';

class CustomGoogleService extends GoogleOpenIDService {

	//protected $jsArguments = array('popup' => array('width' => 450, 'height' => 450));

	protected $requiredAttributes = array(
		'firstname' => array(
			'firstname',
			'namePerson/first'
		),
		'lastname' => array(
			'lastname',
			'namePerson/last'
		),
		'email' => array(
			'email',
			'contact/email'
		),
	);

	protected function fetchAttributes () {
		if ( empty($this->attributes['name']) ) {
			$this->attributes['name'] = $this->attributes['firstname'] . ' ' . $this->attributes['lastname'];
		}
	}
}