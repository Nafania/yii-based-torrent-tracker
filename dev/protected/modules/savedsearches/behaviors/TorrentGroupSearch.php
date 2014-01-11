<?php

/**
 * Поведение для испльзования поиска в TorrentGroup
 *
 * Class TorrentGroupSearch
 */
class TorrentGroupSearch extends CActiveRecordBehavior {
	public function setSearchSettings () {
		$this->_setCriteria();
		return $this->getOwner();
	}

	public function _setCriteria () {
		$searchData = Yii::app()->getUser()->getSavedSearchData('modules_torrents_models_TorrentGroup');

		$category = Yii::app()->getRequest()->getParam('category', $searchData['category']);
		$tags = Yii::app()->getRequest()->getParam('tags', $searchData['tags']);
		$notTags = Yii::app()->getRequest()->getParam('notTags', $searchData['notTags']);
		$search = Yii::app()->getRequest()->getParam('search', $searchData['search']);
		$sort = Yii::app()->getRequest()->getParam('sort', $searchData['sort']);
		$period = Yii::app()->getRequest()->getParam('period', $searchData['period']);

		/**
		 * @var $owner modules\torrents\models\TorrentGroup
		 */
		$owner = $this->getOwner();
		$alias = $owner->getTableAlias();

		$this->getOwner()->searchWithText($search);
		$this->getOwner()->searchWithTags($tags);
		$this->getOwner()->searchWithNotTags($notTags);
		$this->getOwner()->searchWithCategory($category);

		/**
		 * подключаем таблицу счетчиков только если запрошена сортировка по счетчикам
		 */
		if ( strpos($sort, 'commentsCount') !== false ) {
			$_criteria = new CDbCriteria();
			$_criteria->with = 'commentsCount';
			$this->getOwner()->getDbCriteria()->mergeWith($_criteria);
		}
		/**
		 * подключаем таблицу рейтингов
		 */
		if ( strpos($sort, 'rating') !== false ) {
			$_criteria = new CDbCriteria();
			$_criteria->with = 'rating';
			$this->getOwner()->getDbCriteria()->mergeWith($_criteria);
		}

		$this->getOwner()->getDbCriteria()->mergeWith(modules\torrents\models\TorrentGroup::model()->getPeriodCriteria($period));

		list($sortColumn, $sortDesc) = $this->_getSort($sort);
		$criteria = new CDbCriteria();
		if ( $sortColumn ) {
			$criteria->order = $sortColumn . ' ' . ($sortDesc ? 'DESC' : 'ASC');
		}
		else {
			$criteria->order = $alias . '.mtime DESC';
		}
		$this->getOwner()->getDbCriteria()->mergeWith($criteria);
	}

	protected function _getSort ( $sort ) {
		$sortType = $sortDesc = false;
		if ( $sort ) {
			$bits = explode('.', $sort);
			if ( $bits[0] ) {
				$sortType = $bits[0];
			}
			if ( isset($bits[1]) && $bits[1] == 'desc' ) {
				$sortDesc = true;
			}
		}

		$sortColumns = modules\torrents\models\TorrentGroup::getSortColums();
		$_sortColumns = array();
		foreach ( $sortColumns AS $key => $val ) {
			list($_sort, $_sortOrder) = explode('.', $key);
			$_sortColumns[] = $_sort;
		}

		if ( !in_array($sortType, $_sortColumns) ) {
			$sortType = $sortDesc = false;
		}

		$sort = $this->getOwner()->search()->sort;

		if ( $sortType && $var = $sort->attributes[$sortType] ) {
			return array(
				( is_array($var) ? $var['asc'] : $var ),
				$sortDesc
			);
		}

		return array(
			$sortType,
			$sortDesc
		);
	}
}