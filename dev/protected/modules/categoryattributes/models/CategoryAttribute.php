<?php

/**
 * This is the model class for table "attributes".
 *
 * The followings are the available columns in table 'attributes':
 * @property integer $catId
 * @property integer $attrId
 */
class CategoryAttribute extends EActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Category the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'categoryAttributes';
	}

	public function forCat ( $catId ) {
		$this->getDbCriteria()->mergeWith(array(
		                                       'condition'=> 'catId = ' . (int) $catId,
		                                  ));

		return $this;
	}
}