<?php

/**
 * This is the model class for table "blogposts".
 *
 * The followings are the available columns in table 'blogposts':
 * @property integer $id
 * @property string  $title
 * @property string  $text
 * @property integer $blogId
 * @property integer $ownerId
 * @property integer $ctime
 * @property integer $mtime
 * @property Blog    blog
 */
class BlogPost extends EActiveRecord {

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return BlogPost the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'blogPosts';
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
				'text',
				'filter',
				'filter' => array(
					new CHtmlPurifier(),
					'purify'
				)
			),
			array(
				'title',
				'length',
				'max' => 255
			),
			array(
				'id, title, text, blogId, ownerId, groupId, disableComments, private, hided, ctime',
				'safe',
				'on' => 'adminSearch'
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, title, text, blogId, ownerId, groupId, disableComments, private, hided, ctime',
				'safe',
				'on' => 'search'
			),
		);
	}

	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array(
			     'blog' => array(
				     self::BELONGS_TO,
				     'Blog',
				     'blogId'
			     ),
			     'user' => array(
				     self::BELONGS_TO,
				     'User',
				     'ownerId'
			     ),
			));
	}

	public function behaviors () {
		return CMap::mergeArray(parent::behaviors(),
			array(
			     'SlugBehavior' => array(
				     'class'           => 'application.extensions.SlugBehavior.aii.behaviors.SlugBehavior',
				     'sourceAttribute' => 'title',
				     'slugAttribute'   => 'slug',
				     'mode'            => 'translit',
			     ),
			));
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'      => 'ID',
			'title'   => 'Title',
			'text'    => 'Text',
			'blogId'  => 'Blog',
			'ownerId' => 'Owner',
			'ctime'   => 'Ctime',
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

		$criteria->compare('t.id', $this->id);
		$criteria->compare('blogId', $this->blogId);
		$criteria->compare('ownerId', $this->ownerId);
		$criteria->compare('ctime', $this->ctime);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                           'sort'     => array(
			                                           'defaultOrder' => 'ctime DESC'
		                                           )
		                                      ));
	}

	public function forBlog ( $blogId ) {
		$criteria = new CDbCriteria();
		$criteria->condition = 'blogId = :blogId';
		$criteria->params = array(
			':blogId' => (int) $blogId
		);

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}


	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->ownerId = Yii::app()->getUser()->getId();
			}
			else {
				$this->mtime = time();
			}

			return true;
		}
	}

	public function getId () {
		return $this->id;
	}

	public function getTitle ( $encode = true ) {
		return ($encode ? CHtml::encode($this->title) : $this->title);
	}

	public function getText () {
		return $this->text;
	}

	public function getUrl () {
		return array(
			'/blogs/post/view',
			'id' => $this->getId(),
			'title' => $this->getSlugTitle(),
		);
	}

	public function getCtime ( $format = false ) {
		if ( $format ) {
			return date($format, $this->ctime);
		}
		return $this->ctime;
	}

	//search functions
	public function searchWithTags ( $tags = false ) {
		if ( $tags ) {
			$this->taggedWith($tags);
		}
	}

	public function searchWithText ( $search = '' ) {
		if ( $search ) {
			$criteria = new CDbCriteria();
			$criteria->with = 'torrents';
			$this->getDbCriteria()->mergeWith($criteria);

			$this->withEavAttributes(array($search));
		}
	}
}