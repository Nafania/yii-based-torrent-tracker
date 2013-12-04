<?php
use modules\blogs\models AS models;

class NewsBlock extends CWidget {
	public $limit = 5;

	public function run () {
		Yii::import('application.modules.blogs.models.*');

		$criteria = new CDbCriteria();
		$criteria->order = 'pinned DESC, ctime DESC';
		$criteria->condition = 'blogId = :blogId';
		$criteria->params = array(
			':blogId' => Yii::app()->config->get('blogsModule.newsBlogId')
		);
		$criteria->limit = $this->limit;

		$news = models\BlogPost::model()->findAll($criteria);

		if ( !$news ) {
			return;
		}

		$this->render('newsBlock',
			array(
			     'news' => $news
			));
	}
}