<?php

/**
 * This is the model class for table "torrentGroups".
 *
 * The followings are the available columns in table 'torrentGroups':
 * @property integer $id
 * @property integer $title
 * @property integer $ctime
 * @property string  $picture
 * @property integer $mtime
 * @property Category $category
 * @property uid $uid
 * @property Torrent[] torrents
 */
class TorrentGroup extends EActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return TorrentGroups the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'torrentGroups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return CMap::mergeArray(parent::rules(),
			array(
			     //array('picture', 'required', 'on' => 'insert'),
			     array(
				     'title',
				     'required',
				     'on' => 'upload'
			     ),
			     array(
				     'id, ctime, mtime',
				     'numerical',
				     'integerOnly' => true
			     ),
			     array(
				     'picture, title',
				     'length',
				     'max' => 255
			     ),
			     // The following rule is used by search().
			     // Please remove those attributes that should not be searched.
			     array(
				     'id, title, ctime, picture, mtime',
				     'safe',
				     'on' => 'search'
			     ),
			));
	}

	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array(
			     'torrents' => array(
				     self::HAS_MANY,
				     'Torrent',
				     'gId'
			     ),
			));
	}

	public function behaviors () {
		return CMap::mergeArray(parent::behaviors(),
			array(
			     'eavAttr' => array(
				     'class'            => 'application.modules.torrents.extensions.eav.EEavBehavior',
				     // Table that stores attributes (required)
				     'tableName'        => 'torrentGroupsEAV',
				     // model id column
				     // Default is 'entity'
				     'entityField'      => 'entity',
				     // attribute name column
				     // Default is 'attribute'
				     'attributeField'   => 'attribute',
				     // attribute value column
				     // Default is 'value'
				     'valueField'       => 'value',
				     // Model FK name
				     // By default taken from primaryKey
				     //'modelTableFk'     => primaryKey,
				     // Array of allowed attributes
				     // All attributes are allowed if not specified
				     // Empty by default
				     'safeAttributes'   => array(),
				     // Attribute prefix. Useful when storing attributes for multiple models in a single table
				     // Empty by default
				     'attributesPrefix' => '',
			     )
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'      => 'ID',
			'title'   => 'Title',
			'ctime'   => 'Ctime',
			'picture' => 'Picture',
			'mtime'   => 'Mtime',
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
		$criteria->compare('title', $this->title);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('picture', $this->picture, true);
		$criteria->compare('mtime', $this->mtime);
		//$criteria->order = 'mtime DESC';

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                           'sort' => array(
			                                           'defaultOrder' => 'mtime DESC'
		                                           )
		                                      ));
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			$this->mtime = time();

			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->uid = Yii::app()->getUser()->getId();
			}

			return true;
		}
	}

	public function getUrl () {
		//return array('/torrents/default/view', 'id' => $this->getId(), 'title' => $this->getTitle());
		return array(
			'/torrents/default/view',
			'id' => $this->getId()
		);
	}

	public function getTitle () {
		//TODO: get proper name
		$attributes = $this->getEavAttributeKeys();
		return $this->getEavAttribute($attributes[0]->id);
	}

	public function getId () {
		return $this->id;
	}

	public function getDescription () {
		//TODO: get proper description and more fast
		$attributes = $this->getEavAttributeKeys();
		foreach ( $attributes AS $attr ) {
			if ( $attr->type == Attribute::TYPE_TEXTAREA ) {
				return $this->getEavAttribute($attr->id);
			}
		}
	}

	public function getEavAttributesWithKeys () {
		$attributes = $this->getEavAttributeKeys();

		$attrs = array();
		foreach ( $attributes AS $attribute ) {
			$val = $this->getEavAttribute($attribute->getId());
			if ( !$val ) {
				continue;
			}
			$attrs[$attribute->getTitle()] = nl2br($val);
		}

		return $attrs;
	}

	public function getSeparateAttributes () {
		$return = array();
		foreach ( $this->torrents AS $torrent ) {
			$return[] = $torrent->getSeparateAttribute();
		}

		return $return;
	}

	public function getEavAttributeKeys () {
		return $this->category->attrs(array('condition' => 'common = 1'));
	}

	public function getTags () {
		$tags = array();
		foreach ( $this->torrents AS $torrent ) {
			$tags = CMap::mergeArray($tags, $torrent->getTags());
		}
		return array_unique($tags);
	}

	public function searchWithText ( $search ) {
		$this->withEavAttributes(array($search));
	}
}