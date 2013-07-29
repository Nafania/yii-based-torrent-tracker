<?php
/**
 * This is the model class for table "staticPages".
 *
 * The followings are the available columns in table 'staticPages':
 * @property integer $id
 * @property string  $title
 * @property string  $pageTitle
 * @property string  $url
 * @property string  $content
 * @property string  $published
 */

class StaticPage extends EActiveRecord {
	public $cacheTime = 3600;

	const PUBLISHED = 1;
	const NOT_PUBLISHED = 0;

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
		return 'staticPages';
	}

	public function rules () {
		return array(
			array(
				'title, content, url',
				'required'
			),
			array(
				'pageTitle, published',
				'safe'
			)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'                  => Yii::t('staticpagesModule.common', 'Id'),
			'title'               => Yii::t('staticpagesModule.common', 'Title'),
			'pageTitle'           => Yii::t('staticpagesModule.common', 'Page Title'),
			'content'             => Yii::t('staticpagesModule.common', 'Content'),
			'url'                 => Yii::t('staticpagesModule.common', 'Url'),
			'published'                 => Yii::t('staticpagesModule.common', 'Published'),
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
		$criteria->compare('content', $this->content, true);
		$criteria->compare('url', $this->url, true);
		$criteria->compare('pageTitle', $this->pageTitle, true);
		$criteria->compare('published', $this->published);

		return new CActiveDataProvider($this, array(
		                                           'criteria'=> $criteria,
		                                      ));
	}

	public function scopes() {
		return array(
			'published' => array(
				'condition' => 'published = ' . self::PUBLISHED,
			)
		);
	}

	public function adminSearch () {
		return array(
			'columns' => array(
				'id',
				'title',
				'pageTitle',
				'url',
			),
		);
	}

	public function findByUrl ( $url ) {
		return $this->find('url=:url', array(':url'=> $url));
	}

	public function getId () {
		return $this->id;
	}

	public function getTitle () {
		return $this->title;
	}

	public function getUrl () {
		return Yii::app()->createUrl('staticpages/default/index', array('view' => $this->url));
	}

	public function getPageTitle () {
		if ( $this->pageTitle ) {
			return $this->pageTitle;
		}
		return $this->getTitle();
	}

	public function getContent () {
		return $this->content;
	}
}