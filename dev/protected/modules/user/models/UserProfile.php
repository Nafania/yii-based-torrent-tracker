<?php

/**
 * This is the model class for table "userProfiles".
 *
 * The followings are the available columns in table 'userProfiles':
 * @property integer $uid
 * @property string $picture
 */
class UserProfile extends EActiveRecord
{
	public $cacheTime = 3600;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserProfile the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'userProfiles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return CMap::mergeArray(parent::rules(), array(
			array('uid', 'required'),
			array('uid', 'numerical', 'integerOnly'=>true),
			array('picture', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('uid, picture', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return CMap::mergeArray(parent::relations(),
			array(
			     'user' => array(
				     self::HAS_ONE,
				     'User',
				     'uid'
			     )
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'uid' => 'Uid',
			'picture' => Yii::t('userModule.common', 'Avatar'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('uid',$this->uid);
		$criteria->compare('picture',$this->picture,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}