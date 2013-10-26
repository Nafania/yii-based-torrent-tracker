<?php
class NewsBlock extends CWidget {
	public function run () {
		Yii::import('application.modules.news.models.*');

		$criteria = new CDbCriteria();
		$criteria->order = 'pinned DESC, ctime DESC';

		$news = News::model()->findAll($criteria);

		if ( !$news ) {
			return;
		}

		$this->render('newsBlock', array(
		                                'news' => $news
		                           ));
	}
}