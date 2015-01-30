<?php
/**
 * This is the model class for table "staticPages".
 *
 * The followings are the available columns in table 'staticPages':
 * @property integer $id_auto_tag
 * @property integer  $fk_tag
 * @property integer  $fk_category
 */

class AutoTag extends EActiveRecord {
	public $cacheTime = 0;

	/**
	 * Returns the static model of the specified AR class.
	 * @return StaticPage the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'auto_tag';
	}

	public function rules () {
		return [
			[
				'fk_tag, fk_category',
				'required'
			],
		];
	}
}