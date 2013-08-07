<?php

/**
 * This is the model class for table "news".
 *
 * The followings are the available columns in table 'news':
 * @property integer $id
 * @property string  $title
 * @property string  $text
 * @property integer $ctime
 * @property integer $pinned
 * @property integer $owner
 */
class News extends EActiveRecord {
	const PINNED_YES = 1;
	const PINNED_NO = 0;

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return News the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'news';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'title, text',
				'required'
			),
			array(
				'id, ctime, pinned, owner',
				'numerical',
				'integerOnly' => true
			),
			array(
				'title',
				'length',
				'max' => 255
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, title, text, ctime, pinned, owner',
				'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	// NOTE: you may need to adjust the relation name and the related
	// class name for the relations automatically generated below.
	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array(
			     'user' => array(
				     self::BELONGS_TO,
				     'User',
				     'owner'
			     ),
			)

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'     => Yii::t('newsModule.common', 'ID'),
			'title'  => Yii::t('newsModule.common', 'Название'),
			'text'   => Yii::t('newsModule.common', 'Текст'),
			'ctime'  => Yii::t('newsModule.common', 'Время создания'),
			'pinned' => Yii::t('newsModule.common', 'Прикреплена?'),
			'owner'  => Yii::t('newsModule.common', 'Автор'),
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
		$criteria->compare('text', $this->text, true);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('pinned', $this->pinned);
		$criteria->compare('owner', $this->owner);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                           'sort' => array(
			                                           'defaultOrder' => 'pinned DESC, ctime DESC'
		                                           )
		                                      ));
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->owner = Yii::app()->getUser()->getId();
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

	public function getText () {
		return $this->text;
	}

	public function getCtime ( $format = false ) {
		if ( $format ) {
			return date($format, $this->ctime);
		}
		return $this->ctime;
	}
}