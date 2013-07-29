<?php

Yii::setPathOfAlias('dynTree', dirname(__FILE__));

/**
 * NestedTreeActiveRecord
 * Extended ActiveRecord with NestedsetBehavior and some treeManipulation
 *
 * @author Szincsák András <andras@szincsak.hu>
 */
class NestedTreeActiveRecord extends CActiveRecord {

    public $hasManyRoots = false;
    public $rootAttribute = 'root';
    public $leftAttribute = 'lft';
    public $rightAttribute = 'rgt';
    public $levelAttribute = 'level';
    public $titleAttribute = 'name';
    private $_ignoreEvent = false;
    private $_deleted = false;
    private $_id;
    private static $_cached;
    private static $_wc = 0;

    /**
     * Get tree items in array format
     * @param string $url url to item href
     * @return array In tree format.
     */
    public function getTree($url="") {
        $models = $this::model()->findAll(array('order' => $this->leftAttribute));
        if (count($models) == 0)
            throw new CDbException(Yii::t('tree', 'There must be minimum one root record in model `{model}`', array('{model}' => get_class($this::model()))));
        $data = $this->loadTree($models, $url);
        //       print_r($data);echo "<hr>";
        return ($data['data']);
    }

    /**
     * Load items to an array recrusively
     * @param CActiveRecord  $models
     * @param string $url url to item href
     * @param array $NodeArray the recrusive array
     * @return array of the item 
     */
    private function loadTree($models, $url="", $NodeArray=null) {
        $NodeArray['count'] = count($models);
        unset($NodeArray['data']);
        foreach ($models as $i => $node) {
            if (!isset($NodeArray['pk']) || !in_array($node->primaryKey, $NodeArray['pk'])) {
                $NodeArray['pk'][] = $node->primaryKey;
                $NodeArray['data'][$i] = array(
                    'key' => $node->primaryKey,
                    'id' => $node->primaryKey,
                    'title' => $node->nodeTitle,
                    'tooltip' => $node->nodeTitle,
                );
                if ($url)
                    $NodeArray['data'][$i]['href'] = $url . $node->primaryKey;
                if (!$node->isLeaf()) {
                    $inter = $this->loadTree($node->children()->findAll(), $url, $NodeArray);
                    $NodeArray['pk'] = $inter['pk'];
                    $NodeArray['data'][$i]['isFolder'] = 'true';
                    $NodeArray['data'][$i]['children'] = $inter['data'];
                }
                if ($node->isRoot())
                    $NodeArray['data'][$i]['isFolder'] = 'true';
            }
        }
        return $NodeArray;
    }

    /**
     * Generate the next value of the prefixed attribute 
     * 
     * @param string $attribute the name of attribute
     * @param string $prefix the prefix of the value
     * @return string the next value of the prefixed attribute 
     */
    public function getNextMaxValue($attribute='name', $prefix='new_') {
        $SSQL = "SELECT MAX(" . $attribute . ") FROM " . $this->tableName() . " WHERE " . $attribute . " like '$prefix%'";
        $value = Yii::app()->db->createCommand($SSQL)->queryScalar();
        $parts = explode($prefix, $value);
        $value = (isset($parts[1])) ? intval($parts[1]) : 0;
        return $prefix . ($value + 1);
    }

    /**
     * Get node titlte by titleAttribute property
     * @return string the title of the item 
     */
    public function getNodeTitle() {
        return $this->{$this->titleAttribute};
    }

    //ORIGINAL methods

    /**
     * Determines if node is leaf.
     * @return boolean whether the node is leaf.
     */
    public function isLeaf() {
        return $this->{$this->rightAttribute} - $this->{$this->leftAttribute} === 1;
    }

    /**
     * Determines if node is root.
     * @return boolean whether the node is root.
     */
    public function isRoot() {
        return $this->{$this->leftAttribute} == 1;
    }

    /**
     * Determines if node is descendant of subject node.
     * @param CActiveRecord $subj the subject node.
     * @return boolean whether the node is descendant of subject node.
     */
    public function isDescendantOf($subj) {
        $result = ($this->{$this->leftAttribute} > $subj->{$this->leftAttribute})
                && ($this->{$this->rightAttribute} < $subj->{$this->rightAttribute});

        if ($this->hasManyRoots)
            $result = $result && ($this->{$this->rootAttribute} === $subj->{$this->rootAttribute});

        return $result;
    }

    /**
     * Returns if the current node is deleted.
     * @return boolean whether the node is deleted.
     */
    public function getIsDeletedRecord() {
        return $this->_deleted;
    }

    /**
     * Sets if the current node is deleted.
     * @param boolean $value whether the node is deleted.
     */
    public function setIsDeletedRecord($value) {
        $this->_deleted = $value;
    }

    /**
     * Named scope. Gets descendants for node.
     * @param int $depth the depth.
     * @return CActiveRecord the owner.
     */
    public function descendants($depth=null) {
        $db = $this->getDbConnection();
        $criteria = $this->getDbCriteria();
        $alias = $db->quoteColumnName($this->getTableAlias());

        $criteria->mergeWith(array(
            'condition' => $alias . '.' . $db->quoteColumnName($this->leftAttribute) . '>' . $this->{$this->leftAttribute} .
            ' AND ' . $alias . '.' . $db->quoteColumnName($this->rightAttribute) . '<' . $this->{$this->rightAttribute},
            'order' => $alias . '.' . $db->quoteColumnName($this->leftAttribute),
        ));

        if ($depth !== null)
            $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->levelAttribute) . '<=' . ($this->{$this->levelAttribute} + $depth));

        if ($this->hasManyRoots) {
            $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount);
            $criteria->params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
        }

        return $this;
    }

    /**
     * Named scope. Gets children for node (direct descendants only).
     * @return CActiveRecord the owner.
     */
    public function children() {
        return $this->descendants(1);
    }

    /**
     * Named scope. Gets ancestors for node.
     * @param int $depth the depth.
     * @return CActiveRecord the owner.
     */
    public function ancestors($depth=null) {
        $db = $this->getDbConnection();
        $criteria = $this->getDbCriteria();
        $alias = $db->quoteColumnName($this->getTableAlias());

        $criteria->mergeWith(array(
            'condition' => $alias . '.' . $db->quoteColumnName($this->leftAttribute) . '<' . $this->{$this->leftAttribute} .
            ' AND ' . $alias . '.' . $db->quoteColumnName($this->rightAttribute) . '>' . $this->{$this->rightAttribute},
            'order' => $alias . '.' . $db->quoteColumnName($this->leftAttribute),
        ));

        if ($depth !== null)
            $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->levelAttribute) . '>=' . ($this->{$this->levelAttribute} - $depth));

        if ($this->hasManyRoots) {
            $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount);
            $criteria->params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
        }

        return $this;
    }

    /**
     * Named scope. Gets root node(s).
     * @return CActiveRecord the owner.
     */
    public function roots() {
        $db = $this->getDbConnection();
        $this->getDbCriteria()->addCondition($db->quoteColumnName($this->getTableAlias()) . '.' . $db->quoteColumnName($this->leftAttribute) . '=1');

        return $this;
    }

    /**
     * Named scope. Gets parent of node.
     * @return CActiveRecord the owner.
     */
    public function parent() {
        $db = $this->getDbConnection();
        $criteria = $this->getDbCriteria();
        $alias = $db->quoteColumnName($this->getTableAlias());

        $criteria->mergeWith(array(
            'condition' => $alias . '.' . $db->quoteColumnName($this->leftAttribute) . '<' . $this->{$this->leftAttribute} .
            ' AND ' . $alias . '.' . $db->quoteColumnName($this->rightAttribute) . '>' . $this->{$this->rightAttribute},
            'order' => $alias . '.' . $db->quoteColumnName($this->rightAttribute),
        ));

        if ($this->hasManyRoots) {
            $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount);
            $criteria->params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $owner->{$this->rootAttribute};
        }

        return $this;
    }

    /**
     * Named scope. Gets previous sibling of node.
     * @return CActiveRecord the owner.
     */
    public function prev() {
        $db = $this->getDbConnection();
        $criteria = $this->getDbCriteria();
        $alias = $db->quoteColumnName($this->getTableAlias());
        $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->rightAttribute) . '=' . ($this->{$this->leftAttribute} - 1));

        if ($this->hasManyRoots) {
            $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount);
            $criteria->params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
        }

        return $this;
    }

    /**
     * Named scope. Gets next sibling of node.
     * @return CActiveRecord the owner.
     */
    public function next() {
        $db = $this->getDbConnection();
        $criteria = $this->getDbCriteria();
        $alias = $db->quoteColumnName($this->getTableAlias());
        $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->leftAttribute) . '=' . ($this->{$this->rightAttribute} + 1));

        if ($this->hasManyRoots) {
            $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount);
            $criteria->params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
        }

        return $this;
    }

    /**
     * Create root node if multiple-root tree mode. Update node if it's not new.
     * @param boolean $runValidation whether to perform validation.
     * @param boolean $attributes list of attributes.
     * @return boolean whether the saving succeeds.
     */
    public function save($runValidation=true, $attributes=null) {
        if ($runValidation && !$this->validate($attributes))
            return false;

        if ($this->getIsNewRecord())
            return $this->makeRoot($attributes);

        $this->_ignoreEvent = true;
        $result = $this->update($attributes);
        $this->_ignoreEvent = false;

        return $result;
    }

    /**
     * Create root node if multiple-root tree mode. Update node if it's not new.
     * @param boolean $runValidation whether to perform validation.
     * @param boolean $attributes list of attributes.
     * @return boolean whether the saving succeeds.
     */
    public function saveNode($runValidation=true, $attributes=null) {
        return $this->save($runValidation, $attributes);
    }

    /**
     * Deletes node and it's descendants.
     * @return boolean whether the deletion is successful.
     */
    public function deleteNode() {
        if ($this->getIsNewRecord())
            throw new CDbException(Yii::t('ext.NestedDynaTree.message', 'The node cannot be deleted because it is new.'));

        if ($this->getIsDeletedRecord())
            throw new CDbException(Yii::t('yiiext', 'The node cannot be deleted because it is already deleted.'));

        $db = $this->getDbConnection();
        $extTransFlag = $db->getCurrentTransaction();

        if ($extTransFlag === null)
            $transaction = $db->beginTransaction();

        try {
            if ($this->isLeaf()) {
                $this->_ignoreEvent = true;
                $result = $this->delete();
                $this->_ignoreEvent = false;
            } else {
                $condition = $db->quoteColumnName($this->leftAttribute) . '>=' . $this->{$this->leftAttribute} . ' AND ' .
                        $db->quoteColumnName($this->rightAttribute) . '<=' . $this->{$this->rightAttribute};

                $params = array();

                if ($this->hasManyRoots) {
                    $condition.=' AND ' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount;
                    $params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
                }

                $result = $this->deleteAll($condition, $params) > 0;
            }

            if (!$result) {
                if ($extTransFlag === null)
                    $transaction->rollBack();

                return false;
            }

            $this->shiftLeftRight($this->{$this->rightAttribute} + 1, $this->{$this->leftAttribute} - $this->{$this->rightAttribute} - 1);

            if ($extTransFlag === null)
                $transaction->commit();

            $this->correctCachedOnDelete();
        } catch (Exception $e) {
            if ($extTransFlag === null)
                $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * Prepends node to target as first child.
     * @param CActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the prepending succeeds.
     */
    public function prependTo($target, $runValidation=true, $attributes=null) {
        return $this->addNode($target, $target->{$this->leftAttribute} + 1, 1, $runValidation, $attributes);
    }

    /**
     * Prepends target to node as first child.
     * @param CActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the prepending succeeds.
     */
    public function prepend($target, $runValidation=true, $attributes=null) {
        return $target->prependTo($this->getOwner(), $runValidation, $attributes);
    }

    /**
     * Appends node to target as last child.
     * @param CActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the appending succeeds.
     */
    public function appendTo($target, $runValidation=true, $attributes=null) {
        return $this->addNode($target, $target->{$this->rightAttribute}, 1, $runValidation, $attributes);
    }

    /**
     * Appends target to node as last child.
     * @param CActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the appending succeeds.
     */
    public function append($target, $runValidation=true, $attributes=null) {
        return $target->appendTo($this->getOwner(), $runValidation, $attributes);
    }

    /**
     * Inserts node as previous sibling of target.
     * @param CActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the inserting succeeds.
     */
    public function insertBefore($target, $runValidation=true, $attributes=null) {
        return $this->addNode($target, $target->{$this->leftAttribute}, 0, $runValidation, $attributes);
    }

    /**
     * Inserts node as next sibling of target.
     * @param CActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the inserting succeeds.
     */
    public function insertAfter($target, $runValidation=true, $attributes=null) {
        return $this->addNode($target, $target->{$this->rightAttribute} + 1, 0, $runValidation, $attributes);
    }

    /**
     * Move node as previous sibling of target.
     * @param CActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveBefore($target) {
        return $this->moveNode($target, $target->{$this->leftAttribute}, 0);
    }

    /**
     * Move node as next sibling of target.
     * @param CActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveAfter($target) {
        return $this->moveNode($target, $target->{$this->rightAttribute} + 1, 0);
    }

    /**
     * Move node as first child of target.
     * @param CActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveAsFirst($target) {
        return $this->moveNode($target, $target->{$this->leftAttribute} + 1, 1);
    }

    /**
     * Move node as last child of target.
     * @param CActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveAsLast($target) {
        return $this->moveNode($target, $target->{$this->rightAttribute}, 1);
    }

    /**
     * Move node as new root.
     * @return boolean whether the moving succeeds.
     */
    public function moveAsRoot() {
        if (!$this->hasManyRoots)
            throw new CException(Yii::t('yiiext', 'Many roots mode is off.'));

        if ($this->getIsNewRecord())
            throw new CException(Yii::t('yiiext', 'The node should not be new record.'));

        if ($this->getIsDeletedRecord())
            throw new CDbException(Yii::t('yiiext', 'The node should not be deleted.'));

        if ($this->isRoot())
            throw new CException(Yii::t('yiiext', 'The node already is root node.'));

        $db = $this->getDbConnection();
        $extTransFlag = $db->getCurrentTransaction();

        if ($extTransFlag === null)
            $transaction = $db->beginTransaction();

        try {
            $left = $this->{$this->leftAttribute};
            $right = $this->{$this->rightAttribute};
            $levelDelta = 1 - $this->{$this->levelAttribute};
            $delta = 1 - $left;

            $this->updateAll(
                    array(
                $this->leftAttribute => new CDbExpression($db->quoteColumnName($this->leftAttribute) . sprintf('%+d', $delta)),
                $this->rightAttribute => new CDbExpression($db->quoteColumnName($this->rightAttribute) . sprintf('%+d', $delta)),
                $this->levelAttribute => new CDbExpression($db->quoteColumnName($this->levelAttribute) . sprintf('%+d', $levelDelta)),
                $this->rootAttribute => $owner->getPrimaryKey(),
                    ), $db->quoteColumnName($this->leftAttribute) . '>=' . $left . ' AND ' .
                    $db->quoteColumnName($this->rightAttribute) . '<=' . $right . ' AND ' .
                    $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount, array(CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++ => $this->{$this->rootAttribute}));

            $this->shiftLeftRight($right + 1, $left - $right - 1);

            if ($extTransFlag === null)
                $transaction->commit();

            $this->correctCachedOnMoveBetweenTrees(1, $levelDelta, $this->getPrimaryKey());
        } catch (Exception $e) {
            if ($extTransFlag === null)
                $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * Handle 'afterConstruct' event of the owner.
     * @param CEvent $event event parameter.
     */
    public function afterConstruct($event=null) {
        self::$_cached[get_class($this)][$this->_id = self::$_wc++] = $this;
    }

    /**
     * Handle 'afterFind' event of the owner.
     * @param CEvent $event event parameter.
     */
    public function afterFind($event=null) {
        self::$_cached[get_class($this)][$this->_id = self::$_wc++] = $this;
    }

    /**
     * Handle 'beforeSave' event of the owner.
     * @param CEvent $event event parameter.
     * @return boolean.
     */
    public function beforeSave($event=null) {
        if ($this->_ignoreEvent)
            return true;
        else
            throw new CDbException(Yii::t('yiiext', 'You should not use CActiveRecord::save() method when NestedSetBehavior attached.'));
    }

    /**
     * Handle 'beforeDelete' event of the owner.
     * @param CEvent $event event parameter.
     * @return boolean.
     */
    public function beforeDelete($event=null) {
        if ($this->_ignoreEvent)
            return true;
        else
            throw new CDbException(Yii::t('yiiext', 'You should not use CActiveRecord::delete() method when NestedSetBehavior attached.'));
    }

    /**
     * @param int $key.
     * @param int $delta.
     */
    private function shiftLeftRight($key, $delta) {
        $db = $this->getDbConnection();

        foreach (array($this->leftAttribute, $this->rightAttribute) as $attribute) {
            $condition = $db->quoteColumnName($attribute) . '>=' . $key;
            $params = array();

            if ($this->hasManyRoots) {
                $condition.=' AND ' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount;
                $params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
            }

            $this->updateAll(array($attribute => new CDbExpression($db->quoteColumnName($attribute) . sprintf('%+d', $delta))), $condition, $params);
        }
    }

    /**
     * @param CActiveRecord $target.
     * @param int $key.
     * @param int $levelUp.
     * @param boolean $runValidation.
     * @param array $attributes.
     * @return boolean.
     */
    private function addNode($target, $key, $levelUp, $runValidation, $attributes) {

        if (!$this->isNewRecord)
            throw new CDbException(Yii::t('yiiext', 'The node cannot be inserted because it is not new.'));

        if ($this->getIsDeletedRecord())
            throw new CDbException(Yii::t('yiiext', 'The node cannot be inserted because it is deleted.'));

        if ($target->getIsDeletedRecord())
            throw new CDbException(Yii::t('yiiext', 'The node cannot be inserted because target node is deleted.'));

        if ($this->equals($target))
            throw new CException(Yii::t('yiiext', 'The target node should not be self.'));

        if (!$levelUp && $target->isRoot())
            throw new CException(Yii::t('yiiext', 'The target node should not be root.'));

        if ($runValidation && !$this->validate())
            return false;

        if ($this->hasManyRoots)
            $this->{$this->rootAttribute} = $target->{$this->rootAttribute};

        $db = $this->getDbConnection();
        $extTransFlag = $db->getCurrentTransaction();

        if ($extTransFlag === null)
            $transaction = $db->beginTransaction();

        try {
            $this->shiftLeftRight($key, 2);
            $this->{$this->leftAttribute} = $key;
            $this->{$this->rightAttribute} = $key + 1;
            $this->{$this->levelAttribute} = $target->{$this->levelAttribute} + $levelUp;
            $this->_ignoreEvent = true;
            $result = $this->insert($attributes);
            $this->_ignoreEvent = false;

            if (!$result) {
                if ($extTransFlag === null)
                    $transaction->rollBack();

                return false;
            }

            if ($extTransFlag === null)
                $transaction->commit();

            $this->correctCachedOnAddNode($key);
        } catch (Exception $e) {
            if ($extTransFlag === null)
                $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * @param array $attributes.
     * @return boolean.
     */
    private function makeRoot($attributes) {
        $this->{$this->leftAttribute} = 1;
        $this->{$this->rightAttribute} = 2;
        $this->{$this->levelAttribute} = 1;

        if ($this->hasManyRoots) {
            $db = $this->getDbConnection();
            $extTransFlag = $db->getCurrentTransaction();

            if ($extTransFlag === null)
                $transaction = $db->beginTransaction();

            try {
                $this->_ignoreEvent = true;
                $result = $this->insert($attributes);
                $this->_ignoreEvent = false;

                if (!$result) {
                    if ($extTransFlag === null)
                        $transaction->rollBack();

                    return false;
                }

                $pk = $this->{$this->rootAttribute} = $this->getPrimaryKey();
                $this->updateByPk($pk, array($this->rootAttribute => $pk));

                if ($extTransFlag === null)
                    $transaction->commit();
            } catch (Exception $e) {
                if ($extTransFlag === null)
                    $transaction->rollBack();

                throw $e;
            }
        }
        else {
            if ($this->roots()->exists())
                throw new CException(Yii::t('yiiext', 'Cannot create more than one root in single root mode.'));

            $this->_ignoreEvent = true;
            $result = $this->insert($attributes);
            $this->_ignoreEvent = false;

            if (!$result)
                return false;
        }

        return true;
    }

    /**
     * @param CActiveRecord $target.
     * @param int $key.
     * @param int $levelUp.
     * @return boolean.
     */
    private function moveNode($target, $key, $levelUp) {

        if ($this->getIsNewRecord())
            throw new CException(Yii::t('yiiext', 'The node should not be new record.'));

        if ($this->getIsDeletedRecord())
            throw new CDbException(Yii::t('yiiext', 'The node should not be deleted.'));

        if ($target->getIsDeletedRecord())
            throw new CDbException(Yii::t('yiiext', 'The target node should not be deleted.'));

        if ($this->equals($target))
            throw new CException(Yii::t('yiiext', 'The target node should not be self.'));

        if ($target->isDescendantOf($this))
            throw new CException(Yii::t('yiiext', 'The target node should not be descendant.'));

        if (!$levelUp && $target->isRoot())
            throw new CException(Yii::t('NestedDynaTree.tree', 'The target node should not be root.'));

        $db = $this->getDbConnection();
        $extTransFlag = $db->getCurrentTransaction();

        if ($extTransFlag === null)
            $transaction = $db->beginTransaction();

        try {
            $left = $this->{$this->leftAttribute};
            $right = $this->{$this->rightAttribute};
            $levelDelta = $target->{$this->levelAttribute} - $this->{$this->levelAttribute} + $levelUp;

            if ($this->hasManyRoots && $this->{$this->rootAttribute} !== $target->{$this->rootAttribute}) {
                foreach (array($this->leftAttribute, $this->rightAttribute) as $attribute) {
                    $this->updateAll(array($attribute => new CDbExpression($db->quoteColumnName($attribute) . sprintf('%+d', $right - $left + 1))), $db->quoteColumnName($attribute) . '>=' . $key . ' AND ' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount, array(CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++ => $target->{$this->rootAttribute}));
                }

                $delta = $key - $left;

                $this->updateAll(
                        array(
                    $this->leftAttribute => new CDbExpression($db->quoteColumnName($this->leftAttribute) . sprintf('%+d', $delta)),
                    $this->rightAttribute => new CDbExpression($db->quoteColumnName($this->rightAttribute) . sprintf('%+d', $delta)),
                    $this->levelAttribute => new CDbExpression($db->quoteColumnName($this->levelAttribute) . sprintf('%+d', $levelDelta)),
                    $this->rootAttribute => $target->{$this->rootAttribute},
                        ), $db->quoteColumnName($this->leftAttribute) . '>=' . $left . ' AND ' .
                        $db->quoteColumnName($this->rightAttribute) . '<=' . $right . ' AND ' .
                        $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount, array(CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++ => $this->{$this->rootAttribute}));

                $this->shiftLeftRight($right + 1, $left - $right - 1);

                if ($extTransFlag === null)
                    $transaction->commit();

                $this->correctCachedOnMoveBetweenTrees($key, $levelDelta, $target->{$this->rootAttribute});
            }
            else {
                $delta = $right - $left + 1;
                $this->shiftLeftRight($key, $delta);

                if ($left >= $key) {
                    $left+=$delta;
                    $right+=$delta;
                }

                $condition = $db->quoteColumnName($this->leftAttribute) . '>=' . $left . ' AND ' . $db->quoteColumnName($this->rightAttribute) . '<=' . $right;
                $params = array();

                if ($this->hasManyRoots) {
                    $condition.=' AND ' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount;
                    $params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
                }

                $this->updateAll(array($this->levelAttribute => new CDbExpression($db->quoteColumnName($this->levelAttribute) . sprintf('%+d', $levelDelta))), $condition, $params);

                foreach (array($this->leftAttribute, $this->rightAttribute) as $attribute) {
                    $condition = $db->quoteColumnName($attribute) . '>=' . $left . ' AND ' . $db->quoteColumnName($attribute) . '<=' . $right;
                    $params = array();

                    if ($this->hasManyRoots) {
                        $condition.=' AND ' . $db->quoteColumnName($this->rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount;
                        $params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $this->{$this->rootAttribute};
                    }

                    $this->updateAll(array($attribute => new CDbExpression($db->quoteColumnName($attribute) . sprintf('%+d', $key - $left))), $condition, $params);
                }

                $this->shiftLeftRight($right + 1, -$delta);

                if ($extTransFlag === null)
                    $transaction->commit();

                $this->correctCachedOnMoveNode($key, $levelDelta);
            }
        } catch (Exception $e) {
            if ($extTransFlag === null)
                $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * Correct cache for {@link NestedSetBehavior::delete()} and {@link NestedSetBehavior::deleteNode()}.
     */
    private function correctCachedOnDelete() {
        $left = $this->{$this->leftAttribute};
        $right = $this->{$this->rightAttribute};
        $key = $right + 1;
        $delta = $left - $right - 1;

        foreach (self::$_cached[get_class($this)] as $node) {
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
                continue;

            if ($this->hasManyRoots && $this->{$this->rootAttribute} !== $node->{$this->rootAttribute})
                continue;

            if ($node->{$this->leftAttribute} >= $left && $node->{$this->rightAttribute} <= $right)
                $node->setIsDeletedRecord(true);
            else {
                if ($node->{$this->leftAttribute} >= $key)
                    $node->{$this->leftAttribute}+=$delta;

                if ($node->{$this->rightAttribute} >= $key)
                    $node->{$this->rightAttribute}+=$delta;
            }
        }
    }

    /**
     * Correct cache for {@link NestedSetBehavior::addNode()}.
     * @param int $key.
     */
    private function correctCachedOnAddNode($key) {

        foreach (self::$_cached[get_class($this)] as $node) {
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
                continue;

            if ($this->hasManyRoots && $this->{$this->rootAttribute} !== $node->{$this->rootAttribute})
                continue;

            if ($this === $node)
                continue;

            if ($node->{$this->leftAttribute} >= $key)
                $node->{$this->leftAttribute}+=2;

            if ($node->{$this->rightAttribute} >= $key)
                $node->{$this->rightAttribute}+=2;
        }
    }

    /**
     * Correct cache for {@link NestedSetBehavior::moveNode()}. 
     * @param int $key.
     * @param int $levelDelta.
     */
    private function correctCachedOnMoveNode($key, $levelDelta) {
        $left = $this->{$this->leftAttribute};
        $right = $this->{$this->rightAttribute};
        $delta = $right - $left + 1;

        if ($left >= $key) {
            $left+=$delta;
            $right+=$delta;
        }

        $delta2 = $key - $left;

        foreach (self::$_cached[get_class($this)] as $node) {
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
                continue;

            if ($this->hasManyRoots && $this->{$this->rootAttribute} !== $node->{$this->rootAttribute})
                continue;

            if ($node->{$this->leftAttribute} >= $key)
                $node->{$this->leftAttribute}+=$delta;

            if ($node->{$this->rightAttribute} >= $key)
                $node->{$this->rightAttribute}+=$delta;

            if ($node->{$this->leftAttribute} >= $left && $node->{$this->rightAttribute} <= $right)
                $node->{$this->levelAttribute}+=$levelDelta;

            if ($node->{$this->leftAttribute} >= $left && $node->{$this->leftAttribute} <= $right)
                $node->{$this->leftAttribute}+=$delta2;

            if ($node->{$this->rightAttribute} >= $left && $node->{$this->rightAttribute} <= $right)
                $node->{$this->rightAttribute}+=$delta2;

            if ($node->{$this->leftAttribute} >= $right + 1)
                $node->{$this->leftAttribute}-=$delta;

            if ($node->{$this->rightAttribute} >= $right + 1)
                $node->{$this->rightAttribute}-=$delta;
        }
    }

    /**
     * Correct cache for {@link NestedSetBehavior::moveNode()}.
     * @param int $key.
     * @param int $levelDelta.
     * @param int $root.
     */
    private function correctCachedOnMoveBetweenTrees($key, $levelDelta, $root) {
        $left = $this->{$this->leftAttribute};
        $right = $this->{$this->rightAttribute};
        $delta = $right - $left + 1;
        $delta2 = $key - $left;
        $delta3 = $left - $right - 1;

        foreach (self::$_cached[get_class($this)] as $node) {
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
                continue;

            if ($node->{$this->rootAttribute} === $root) {
                if ($node->{$this->leftAttribute} >= $key)
                    $node->{$this->leftAttribute}+=$delta;

                if ($node->{$this->rightAttribute} >= $key)
                    $node->{$this->rightAttribute}+=$delta;
            }
            else if ($node->{$this->rootAttribute} === $this->{$this->rootAttribute}) {
                if ($node->{$this->leftAttribute} >= $left && $node->{$this->rightAttribute} <= $right) {
                    $node->{$this->leftAttribute}+=$delta2;
                    $node->{$this->rightAttribute}+=$delta2;
                    $node->{$this->levelAttribute}+=$levelDelta;
                    $node->{$this->rootAttribute} = $root;
                } else {
                    if ($node->{$this->leftAttribute} >= $right + 1)
                        $node->{$this->leftAttribute}+=$delta3;

                    if ($node->{$this->rightAttribute} >= $right + 1)
                        $node->{$this->rightAttribute}+=$delta3;
                }
            }
        }
    }

    /**
     * Destructor.
     */
    public function __destruct() {
        unset(self::$_cached[get_class($this)][$this->_id]);
    }

}

?>
