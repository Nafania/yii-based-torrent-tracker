<?php
Yii::import('modules.savedsearches.behaviors.*');

class TorrentSearch extends TorrentGroupSearch {

	public function _setCriteria () {
		$searchData = Yii::app()->getUser()->getSavedSearchData('modules_torrents_models_TorrentGroup');

		$category = Yii::app()->getRequest()->getParam('category', $searchData['category']);
		$tags = Yii::app()->getRequest()->getParam('tags', $searchData['tags']);
		$notTags = Yii::app()->getRequest()->getParam('notTags', $searchData['notTags']);
		$search = Yii::app()->getRequest()->getParam('search', $searchData['search']);
		$sort = Yii::app()->getRequest()->getParam('sort', $searchData['sort']);
		$period = Yii::app()->getRequest()->getParam('period', $searchData['period']);

		/**
		 * @var $owner modules\torrents\models\Torrent
		 */
		$owner = $this->getOwner();
		$alias = $owner->getTableAlias();

		$criteria = new CDbCriteria();
		$criteria->with = 'torrentGroup';
		$this->getOwner()->getDbCriteria()->mergeWith($criteria);

		$this->getOwner()->searchWithText($search);
		$this->getOwner()->searchWithTags($tags);
		$this->getOwner()->searchWithNotTags($notTags);
		$this->getOwner()->searchWithCategory($category);

		/**
		 * подключаем таблицу счетчиков только если запрошена сортировка по счетчикам
		 */
		if ( strpos($sort, 'commentsCount') !== false ) {
			$_criteria = new CDbCriteria();
			$_criteria->with = 'torrentGroup.commentsCount';
			$this->getOwner()->getDbCriteria()->mergeWith($_criteria);
		}
		/**
		 * подключаем таблицу рейтингов
		 */
		if ( strpos($sort, 'rating') !== false ) {
			$_criteria = new CDbCriteria();
			$_criteria->with = 'torrentGroup.rating';
			$this->getOwner()->getDbCriteria()->mergeWith($_criteria);
		}

		if ( $period ) {
			$this->getOwner()->getDbCriteria()->mergeWith(modules\torrents\models\TorrentGroup::model()->getPeriodCriteria($period, 'torrentGroup'));
		}

		list($sortColumn, $sortDesc) = $this->_getSort($sort);
		$criteria = new CDbCriteria();
		if ( $sortColumn ) {
			$criteria->order = $sortColumn . ' ' . ($sortDesc ? 'DESC' : 'ASC');
		}
		else {
			$criteria->order = $alias . '.ctime DESC';
		}
		$this->getOwner()->getDbCriteria()->mergeWith($criteria);
	}
}