<?php
/**
 * Поведение для испльзования поиска в TorrentGroup
 *
 * Class TorrentGroupSearch
 */
class TorrentGroupSearch extends CActiveRecordBehavior {
	public function setSearchSettings () {
		$this->_setCriteria();
	}

	private function _setCriteria () {
		$searchData = Yii::app()->getUser()->getSavedSearchData('TorrentGroup');

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
			$_criteria->select = $alias . '.*, cc.count AS commentsCount';
			$_criteria->join = 'LEFT JOIN {{commentCounts}} cc ON ( cc.modelName = :modelName AND cc.modelId = ' . $alias . '.id)';
			$_criteria->params[':modelName'] = $owner->resolveClassName();
			$this->getOwner()->getDbCriteria()->mergeWith($_criteria);
		}
		/**
		 * подключаем таблицу рейтингов
		 */
		if ( strpos($sort, 'rating') !== false ) {
			$_criteria = new CDbCriteria();
			$_criteria->select = $alias . '.*, r.rating AS rating';
			$_criteria->join = 'LEFT JOIN {{ratings}} r ON ( r.modelName = :modelName AND r.modelId = ' . $alias . '.id)';
			$_criteria->params[':modelName'] = $owner->resolveClassName();
			$this->getOwner()->getDbCriteria()->mergeWith($_criteria);
		}

		$this->getOwner()->getDbCriteria()->mergeWith(modules\torrents\models\TorrentGroup::getPeriodCriteria($period));

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

	private function _getSort ( $sort ) {
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

		return array(
			$sortType,
			$sortDesc
		);
	}
}