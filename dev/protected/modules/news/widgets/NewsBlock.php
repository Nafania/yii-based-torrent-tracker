<?php
class NewsBlock extends CWidget {
	public function run () {
		Yii::import('application.modules.news.models.*');

		$criteria = new CDbCriteria();
		$criteria->order = 'pinned DESC, ctime DESC';

		$news = News::model()->findAll($criteria);

		$this->render('newsBlock', array(
		                                'news' => $news
		                           ));
	}
}