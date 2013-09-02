<?php
/**
 * AdjacencyListBehavior
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @link   https://github.com/yiiext/adjacency-list-behavior
 * @author Jon Langevin <intel352@gmail.com>
 * @link   https://github.com/intel352/adjacency-list-behavior
 */

/**
 * Provides adjacency list functionality for a model.
 *
 * @version 0.30
 * @package yiiext.behaviors.model.trees.al
 */
class AdjacencyListBehavior extends CActiveRecordBehavior {
	const PARENT = 'parent';
	const CHILDREN = 'children';
	const BOTH = 'both';

	public $hasLevel = false;
	public $hasWeight = false;
	public $parentAttribute = 'parent_id';
	public $levelAttribute = 'level';
	public $weightAttribute = 'weight';
	private $_ignoreEvent = false;
	private $_deleted = false;
	private $_id;

	/**
	 * Named scope. Gets ancestors for node.
	 *
	 * @param int depth
	 *
	 * @return CActiveRecord the owner
	 */
	public function ancestors ( $depth = null ) {
		return $this->genealogy(self::PARENT, $depth);
	}

	/**
	 * Named scope. Gets descendants for node.
	 *
	 * @param int depth
	 *
	 * @return CActiveRecord the owner
	 */
	public function descendants ( $depth = null ) {
		return $this->genealogy(self::CHILDREN, $depth);
	}

	/**
	 * Named scope. Gets genealogy (family tree) for node.
	 *
	 * @param int depth
	 *
	 * @return CActiveRecord the owner
	 */
	public function genealogy ( $type = self::BOTH, $depth = null ) {
		/**
		 * @todo check hasLevel if $depth===null
		 */

		if ( $type == self::BOTH ) {
			$type = array(
				self::PARENT,
				self::CHILDREN
			);
		}
		else {
			$type = array($type);
		}

		$owner = $this->getOwner();
		$db = $owner->getDbConnection();
		$criteria = $owner->getDbCriteria();
		$alias = $owner->getTableAlias(true);


		foreach ( $type AS $t ) {
			if ( $owner->getPrimaryKey() == null ) {
				$depth = 1;
			}
			else {
				switch ( $t ) {
					case self::PARENT:
						if ( $owner->{$this->parentAttribute} == null ) {
							continue;
						}
						$criteria->addColumnCondition(array($alias . '.' . $db->quoteColumnName($owner->getTableSchema()->primaryKey) => $owner->{$this->parentAttribute}));
						break;
					case self::CHILDREN:
						$criteria->addColumnCondition(array($alias . '.' . $db->quoteColumnName($this->parentAttribute) => $owner->getTableSchema()->primaryKey));
						break;
				}
			}

			if ( $depth > 0 ) {
				$with = array($t);
			}
			if ( $depth > 1 ) {
				for ( $i = 1; $i < $depth - 1; $i++ ) {
					$with = array(
						$t => array(
							'with'  => $with,
							'alias' => $t . $i,
						),
					);
				}
			}
			if ( !empty($with) ) {
				$criteria->mergeWith(array('with' => $with));
			}
		}

		return $owner;
	}

	/**
	 * Named scope. Gets children for node (direct descendants only).
	 * @return CActiveRecord the owner
	 */
	public function withChildren () {
		return $this->descendants(1);
	}

	public function roots () {
		$owner = $this->getOwner();
		$db = $owner->getDbConnection();
		$criteria = $owner->getDbCriteria();
		$alias = $owner->getTableAlias(true);

		$criteria->addColumnCondition(array($alias . '.' . $db->quoteColumnName($this->parentAttribute) => null));
		return $owner;
	}

	/**
	 * Gets root node.
	 * @todo Is the original intention here to get the root of the current tree? If so, this logic fails
	 * @todo Using level, build query condition instead of looping records
	 * @return CActiveRecord the record found. Null if no record is found
	 */
	public function getRoot () {
		$owner = $this->getOwner();
		#$db=$owner->getDbConnection();
		#$owner->getDbCriteria()->addColumnCondition(array($owner->getTableAlias(true).'.'.$db->quoteColumnName($this->parentAttribute)=>null));

		if ( !$this->getParent() ) {
			return $owner;
		}
		else {
			return $this->getParent()->getRoot();
		}
	}

	/**
	 * Gets record of node parent.
	 * @return CActiveRecord the record found. Null if no record is found
	 */
	public function getParent () {
		/**
		 * Return the record using already defined relationship, fall back to Yii AR handling
		 */
		return $this->getOwner()->{self::PARENT};
	}

	/**
	 * Gets record of node children.
	 * @return CActiveRecord the records found. Null if no record is found
	 */
	public function getChildren () {
		/**
		 * Return the record using already defined relationship, fall back to Yii AR handling
		 */
		return $this->getOwner()->{self::CHILDREN};
	}

	/**
	 * Gets record of previous sibling.
	 * @return CActiveRecord the record found. Null if no record is found
	 */
	public function getPrevSibling () {
		//only if level future is on
	}

	/**
	 * Gets record of next sibling.
	 * @return CActiveRecord the record found. Null if no record is found
	 */
	public function getNextSibling () {
		//only if level future is on
	}

	/**
	 * Update node if it's not new.
	 * @return boolean whether the saving succeeds
	 */
	public function save ( $runValidation = true, $attributes = null ) {
		$owner = $this->getOwner();

		if ( $runValidation && !$owner->validate($attributes) ) {
			return false;
		}

		$this->_ignoreEvent = true;
		if ( $owner->getIsNewRecord() ) {
			return $owner->save();
		}
		else {
			$result = $owner->update($attributes);
		}
		$this->_ignoreEvent = false;

		return $result;
	}

	public function saveNode ( $runValidation = true, $attributes = null ) {
		return $this->save($runValidation, $attributes);
	}

	/**
	 * Deletes node and it's descendants.
	 * @return boolean whether the deletion is successful
	 */
	public function delete () {
		// cascad ?
	}

	public function deleteNode () {
		return $this->delete();
	}

	/**
	 * Appends node to target as last child.
	 * @return boolean whether the appending succeeds
	 */
	public function appendTo ( $target, $runValidation = true, $attributes = null ) {
	}

	/**
	 * Appends target to node as last child.
	 * @return boolean whether the appending succeeds
	 */
	public function append ( $target, $runValidation = true, $attributes = null ) {
		return $target->appendTo($this->getOwner(), $runValidation, $attributes);
	}

	/**
	 * Move node as last child of target.
	 * @return boolean whether the moving succeeds
	 */
	public function moveAsLast ( $target ) {
		// check hasWeight
	}

	/**
	 * Determines if node is descendant of subject node.
	 * @return boolean
	 */
	public function isDescendantOf ( $subj ) {
	}

	/**
	 * Determines if node is leaf.
	 * @return boolean
	 */
	public function isLeaf () {
	}

	public function getIsDeletedRecord () {
		return $this->_deleted;
	}

	public function setIsDeletedRecord ( $value ) {
		$this->_deleted = $value;
	}

	public function attach ( $owner ) {
		parent::attach($owner);

		$owner = $this->getOwner();
		/**
		 * Adding relation types
		 */
		$owner->getMetaData()->addRelation(self::PARENT,
			array(
			     CActiveRecord::HAS_ONE,
			     get_class($owner),
			     $this->parentAttribute
			));
		$owner->getMetaData()->addRelation(self::CHILDREN,
			array(
			     CActiveRecord::HAS_MANY,
			     get_class($owner),
			     $this->parentAttribute
			));
	}

	public function beforeSave ( $event ) {
		parent::beforeSave($event);

		if ( $this->_ignoreEvent ) {
			return true;
		}
		else {
			throw new CDbException(Yii::t('yiiext',
				'You should not use CActiveRecord::save() method when AjacencyListBehavior attached.'));
		}
	}

	public function beforeDelete ( $event ) {
		parent::beforeDelete($event);
		if ( $this->_ignoreEvent ) {
			return true;
		}
		else {
			throw new CDbException(Yii::t('yiiext',
				'You should not use CActiveRecord::delete() method when AjacencyListBehavior attached.'));
		}
	}

	private function correctCachedOnDelete () {
	}

	private function correctCachedOnAddNode () {
	}
}