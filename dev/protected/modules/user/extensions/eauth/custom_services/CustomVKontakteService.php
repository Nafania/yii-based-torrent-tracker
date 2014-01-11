<?php
/**
 * An example of extending the provider class.
 *
 * @author  Maxim Zemskov <nodge@yandex.ru>
 * @link    http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)) . '/services/VKontakteOAuthService.php';

class CustomVKontakteService extends VKontakteOAuthService {

	// protected $scope = 'friends';

	protected function fetchAttributes () {
		$info = (array) $this->makeSignedRequest('https://api.vk.com/method/users.get.json',
			array(
				'query' => array(
					'uids'   => $this->uid,
					//'fields' => '', // uid, first_name and last_name is always available
					'fields' => 'nickname, photo, photo_medium, photo_big, photo_rec',
				),
			));

		$info = $info['response'][0];

		$this->attributes['id'] = $info->uid;
		$this->attributes['name'] = $info->first_name . ' ' . $info->last_name;
		$this->attributes['url'] = 'http://vk.com/id' . $info->uid;
		$this->attributes['avatar'] = $info->photo;
		$this->attributes['email'] = '';
	}
}
