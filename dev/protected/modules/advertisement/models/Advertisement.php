<?php

/**
 * This is the model class for table "advertisements".
 *
 * The followings are the available columns in table 'advertisements':
 * @property integer $id
 * @property string  $systemName
 * @property string  $description
 * @property string  $code
 */
class Advertisement extends EActiveRecord {
	public $cacheTime = 3600;
	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'advertisements';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'systemName, description, code',
				'required'
			),
			array(
				'systemName',
				'length',
				'max' => 255
			),
			array(
				'systemName',
				'unique'
			),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array(
				'id, systemName, description, code',
				'safe',
				'on' => 'search'
			),
		);
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
			'id'          => 'ID',
			'systemName'  => 'System Name',
			'description' => 'Description',
			'code'        => 'Code',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search () {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('systemName', $this->systemName, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('code', $this->code, true);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return Advertisement the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	public function getId () {
		return $this->id;
	}
}
