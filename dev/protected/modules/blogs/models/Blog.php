<?php

/**
 * This is the model class for table "blogs".
 *
 * The followings are the available columns in table 'blogs':
 * @property integer $id
 * @property string  $title
 * @property integer $ownerId
 * @property integer $ctime
 * @property string  $description
 */
class Blog extends EActiveRecord {
	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Blog the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'blogs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'title',
				'required'
			),
			array(
				'title',
				'length',
				'max' => 255
			),
			array(
				'description',
				'filter',
				'filter' => array(
					new CHtmlPurifier(),
					'purify'
				)
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, title, ownerId, ctime, description',
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
		return CMap::mergeArray(parent::relations(),
			array(
			     'user' => array(
				     self::BELONGS_TO,
				     'User',
				     'ownerId'
			     ),
			     'posts' => array(
				     self::HAS_MANY,
				     'BlogPost',
				     'blogId'
			     ),
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'          => 'ID',
			'title'       => 'Title',
			'ownerId'     => 'Owner',
			'ctime'       => 'Ctime',
			'description' => 'Description',
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

		$criteria->compare('id', $this->id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('ownerId', $this->ownerId);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('description', $this->description, true);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	public function scopes () {
		return array(
			'forCurrentUser' => array(
				'condition' => 'ownerId = :ownerId',
				'params'    => array(
					':ownerId' => Yii::app()->getUser()->getId(),
				)
			)
		);
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->ownerId = Yii::app()->getUser()->getId();
				$this->ctime = time();
			}

			return true;
		}
	}

	public function getId () {
		return $this->id;
	}

	public function getTitle () {
		return $this->title;
	}

	public function getDescription () {
		return $this->description;
	}

	public function getUrl () {
		return array(
			'/blogs/default/view',
			'id' => $this->getId()
		);
	}
}