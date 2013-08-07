<?php
class TorrentsFlow extends CWidget {
	public function run () {
		$cs = Yii::app()->getClientScript();
		$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/jMyCarousel/css/style.css');
		$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/jMyCarousel/jMyCarousel.js');


		$criteria = new CDbCriteria();
		$criteria->order = 'mtime DESC';
		$criteria->limit = 20;

		$torrentsGroup = TorrentGroup::model()->findAll($criteria);

		$this->render('torrentsFlow',
			array(
			     'torrentsGroup' => $torrentsGroup,
			     'tabs'          => $this->_getTabs(),
			));
	}

	private function _getTabs () {
		$tabs = array();

		$categories = Category::model()->findAll();

		foreach ( $categories AS $key => $category ) {
			if ( !$tabContent = $this->_renderTab($category->getId()) ) {
				continue;
			}
			$tabs[$category->getId()] = array(
				'title'   => $category->getTitle(),
				'content' => $tabContent,
			);
		}

		return $tabs;
	}

	private function _renderTab ( $catId ) {
		$criteria = new CDbCriteria();
		$criteria->order = 'mtime DESC';
		$criteria->limit = 20;
		$criteria->with = 'category';
		$criteria->condition = 'category.id = ' . $catId;

		$torrentsGroup = TorrentGroup::model()->findAll($criteria);

		if ( $torrentsGroup ) {
		return $this->render('_torrentsFlowTab',
			array(
			     'torrentsGroup' => $torrentsGroup,
			     'catId' => $catId,
			), true);
		}
	}
}