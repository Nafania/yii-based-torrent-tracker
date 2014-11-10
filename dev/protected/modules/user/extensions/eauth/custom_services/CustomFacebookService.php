<?php
class CustomFacebookService extends FacebookOAuthService {
	protected $scope = 'email';

	/**
	 * http://developers.facebook.com/docs/reference/api/user/
	 *
	 * @see FacebookOAuthService::fetchAttributes()
	 */
	protected function fetchAttributes() {
		$info = (object) $this->makeSignedRequest('https://graph.facebook.com/me',
			array(
			     'query' => array(
				     'fields' => 'id,name,link,email,picture'
			     )
			));

		$this->attributes['id'] = $info->id;
		$this->attributes['name'] = $info->name;
		$this->attributes['url'] = $info->link;
		$this->attributes['email'] = $info->email;
		$this->attributes['avatar'] = $info->picture;
	}
}
