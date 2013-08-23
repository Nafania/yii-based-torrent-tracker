<?php

/**
 * This is the model class for table "userSocialAccounts".
 *
 * The followings are the available columns in table 'userSocialAccounts':
 * @property integer $uId
 * @property string  $id
 * @property string  $service
 * @property string  $name
 */
class UserSocialAccount extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return UserSocialAccount the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'userSocialAccounts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return CMap::mergeArray(parent::rules(),
			array(
			     //array('uId, id, service', 'required'),
			     array(
				     'id',
				     'unique',
				     'message' => 'Этот аккаунт уже был добавлен на сайт.'
			     ),
			     array(
				     'uId',
				     'numerical',
				     'integerOnly' => true
			     ),
			     array(
				     'id, service, name',
				     'length',
				     'max' => 255
			     ),
			     // The following rule is used by search().
			     // Please remove those attributes that should not be searched.
			     array(
				     'uId, id, service, name',
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
		return CMap::mergeArray(parent::relations(),
			array(
			     'user' => array(
				     self::BELONGS_TO,
				     'User',
				     'uId',
			     )
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'uId'     => 'U',
			'id'      => 'ID',
			'service' => 'Service',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search () {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('uId', $this->uId);
		$criteria->compare('id', $this->id, true);
		$criteria->compare('service', $this->service, true);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}
}