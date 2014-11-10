<?php
/**
 * An example of extending the provider class.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)) . '/services/TwitterOAuthService.php';

class CustomTwitterService extends TwitterOAuthService {

	protected function fetchAttributes() {
		$info = $this->makeSignedRequest('https://api.twitter.com/1.1/account/verify_credentials.json');

		$this->attributes['id'] = $info->id;
		$this->attributes['name'] = $info->name;
		$this->attributes['url'] = 'http://twitter.com/account/redirect_by_id?id=' . $info->id_str;
		$this->attributes['email'] = $info->email;
		$this->attributes['avatar'] = $info->picture;
	}
}