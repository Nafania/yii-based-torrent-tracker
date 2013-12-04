<?php
class TorrentsFlow extends CWidget {
	public $minLimit = 5;

	public function run () {
		$cs = Yii::app()->getClientScript();
		$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/jMyCarousel/css/style.css');
		$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/jMyCarousel/jMyCarousel.js');


		$criteria = new CDbCriteria();
		$criteria->order = 'mtime DESC';
		$criteria->limit = 20;

		$torrentsGroup = modules\torrents\models\TorrentGroup::model()->findAll($criteria);

		$this->render('torrentsFlow',
			array(
			     'torrentsGroup' => $torrentsGroup,
			     'tabs'          => $this->_getTabs(),
			));
	}

	private function _getTabs () {
		$tabs = array();

		$categories = Category::model()->findAll();

		$i = 0;
		foreach ( $categories AS $category ) {
			if ( !$tabContent = $this->_renderTab($category->getId()) ) {
				continue;
			}
			$tabs[] = array(
				'label'   => $category->getTitle(),
				'content' => $tabContent,
				'active' => $i === 0,
			);

			++$i;
		}

		return $tabs;
	}

	private function _renderTab ( $catId ) {
		$criteria = new CDbCriteria();
		$criteria->select = 't.*, r.rating';
		$criteria->order = 'r.rating DESC';
		$criteria->condition = 't.cId = :cId AND mtime > ( UNIX_TIMESTAMP(NOW()) - 14 * 24 * 60 * 60 )';
		$criteria->join = 'LEFT JOIN {{ratings}} r ON ( r.modelName = \'TorrentGroup\' AND r.modelId = t.id)';
		//$criteria->group = 't.id';
		$criteria->limit = 15;
		$criteria->params = array(
			':cId' => $catId,
		);

		$torrentsGroup = modules\torrents\models\TorrentGroup::model()->findAll($criteria);

		if ( sizeof($torrentsGroup) > $this->minLimit ) {
			return $this->render('_torrentsFlowTab',
				array(
				     'torrentsGroup' => $torrentsGroup,
				     'catId'         => $catId,
				),
				true);
		}
	}
}