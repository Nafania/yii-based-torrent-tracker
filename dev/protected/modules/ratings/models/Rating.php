<?php

/**
 * This is the model class for table "ratings".
 *
 * The followings are the available columns in table 'ratings':
 * @property string  $modelName
 * @property integer $modelId
 * @property integer $rating
 */
class Rating extends EActiveRecord {
	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Rating the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'ratings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return CMap::mergeArray(parent::rules(),
			array(
			     array(
				     'modelName, modelId, rating',
				     'required'
			     ),
			     array(
				     'modelId, rating',
				     'numerical',
				     'integerOnly' => true
			     ),
			     array(
				     'modelName',
				     'length',
				     'max' => 255
			     ),
			     // The following rule is used by search().
			     // Please remove those attributes that should not be searched.
			     array(
				     'modelName, modelId, rating',
				     'safe',
				     'on' => 'search'
			     ),
			));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations () {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return CMap::mergeArray(parent::relations(), array());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'modelName' => 'Model Name',
			'modelId'   => 'Model',
			'rating'    => 'Rating',
		);
	}

	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {

			$validator = CValidator::createValidator('unique',
				$this,
				'modelName',
				array(
				     'criteria' => array(
					     'condition' => 'modelId=:modelId',
					     'params'    => array(
						     ':modelId' => $this->modelId,
					     )
				     )
				));
			$this->getValidatorList()->insertAt(0, $validator);

			return true;
		}
		return false;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search () {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('modelName', $this->modelName, true);
		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('rating', $this->rating);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	public function primaryKey() {
		return array('modelName', 'modelId');
	}

	public function getRating () {
		return $this->rating;
	}
}