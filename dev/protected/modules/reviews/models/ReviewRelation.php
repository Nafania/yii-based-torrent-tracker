<?php

/**
 * This is the model class for table "reviewsRelations".
 *
 * The followings are the available columns in table 'reviewsRelations':
 * @property string   $apiName
 * @property integer  $cId
 * @property string   $params
 */
class ReviewRelation extends EActiveRecord {

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Report the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'reviewsRelations';
	}

	/**
	 * @return array|mixed
	 */
	public function getParams () {
		$params = @unserialize($this->params);
		return ( $params ? $params : array() );
	}
}