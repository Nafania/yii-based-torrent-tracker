<?php
use modules\blogs\models AS models;

class LatestPosts extends CWidget {
	public $limit = 10;

	public function run () {
		Yii::import('application.modules.blogs.models.*');

		$criteria = new CDbCriteria();
		$criteria->order = 't.ctime DESC';
		$criteria->limit = $this->limit;
		$criteria->with = array(
			'blog' => array(
				'joinType' => 'inner join',
				'together' => true
			),
			'blog.group:visible' => array(
				'joinType' => 'inner join',
				'together' => true
			)
		);

		$posts = models\BlogPost::model()->onlyVisible()->findAll($criteria);

		$this->render('latestPosts',
			array(
				'posts' => $posts
			));
	}
}