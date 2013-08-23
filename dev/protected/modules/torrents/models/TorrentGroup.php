<?php

/**
 * This is the model class for table "torrentGroups".
 *
 * The followings are the available columns in table 'torrentGroups':
 * @property integer       $id
 * @property integer       $title
 * @property integer       $ctime
 * @property string        $picture
 * @property integer       $mtime
 * @property Category      $category
 * @property integer       $uid
 * @property Torrent[]     torrents
 * @property string        description
 *
 */
class TorrentGroup extends EActiveRecord {
	private $eavAttributes;

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return TorrentGroup the static model class
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
				     'id, ctime, mtime, cId',
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
				     'cacheId'          => 'cache',
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
				     'preload'          => false,
			     )
			),
			array(
			     'getTorrentTitleBehavior' => array(
				     'class' => 'application.modules.torrents.behaviors.GetTorrentTitleBehavior'
			     )
			),
			array(
			     'SlugBehavior' => array(
				     'class'           => 'application.modules.torrents.behaviors.SlugBehavior.aii.behaviors.SlugBehavior',
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
			'id'      => Yii::t('torrentsModule.common', 'Id'),
			'title'   => Yii::t('torrentsModule.common', 'Title'),
			'ctime'   => Yii::t('torrentsModule.common', 'Create time'),
			'picture' => Yii::t('torrentsModule.common', 'Picture'),
			'mtime'   => Yii::t('torrentsModule.common', 'Modify time'),
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
		$criteria->compare('t.title', $this->title);
		$criteria->compare('t.ctime', $this->ctime);
		$criteria->compare('t.picture', $this->picture, true);
		$criteria->compare('t.mtime', $this->mtime);
		//$criteria->order = 'mtime DESC';

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                           'sort'     => array(
			                                           'defaultOrder' => 'mtime DESC'
		                                           )
		                                      ));
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			//$this->mtime = time();

			if ( !defined('IN_CONVERT') ) {
				if ( $this->getIsNewRecord() ) {
					$this->ctime = $this->mtime = time();
					$this->uid = Yii::app()->getUser()->getId();
				}
			}

			return true;
		}
	}

	public function getUrl () {
		//return array('/torrents/default/view', 'id' => $this->getId(), 'title' => $this->getTitle());
		return array(
			'/torrents/default/view',
			'id'    => $this->getId(),
			'title' => $this->getSlugTitle(),
		);
	}

	public function getId () {
		return $this->id;
	}

	public function getDescription () {
		if ( $this->description !== '' ) {
			return $this->description;
		}
		//TODO: get proper description and more fast
		$attributes = $this->getEavAttributeKeys();
		foreach ( $attributes AS $attr ) {
			if ( $attr->type == Attribute::TYPE_TEXTAREA ) {
				$description = $this->getEavAttribute($attr->id);
				$this->saveAttributes(array('description' => $description));
				return $description;
			}
		}
	}

	public function getEavAttributesWithKeys () {
		$attributes = $this->getEavAttributeKeys();

		$ids = array();
		foreach ( $attributes AS $attribute ) {
			$ids[] = $attribute->getId();
		}

		$attrs = $this->getEavAttributes($ids);

		$i = 0;
		$return = array();
		foreach ( $attrs AS $val ) {
			$attribute = $attributes[$i];
			++$i;
			if ( !$val ) {
				continue;
			}

			$prepend = ($attribute->prepend ? $attribute->prepend . ' ' : '');
			$append = ($attribute->append ? ' ' . $attribute->append : '');
			$return[$attribute->getTitle()] = $prepend . nl2br($val) . $append;

		}

		return $return;
	}

	public function getSeparateAttributes () {
		$return = array();
		foreach ( $this->torrents AS $torrent ) {
			$return[$torrent->getId()] = $torrent->getSeparateAttribute();
		}

		return $return;
	}

	public function getSeparateAttribute ( $id ) {
		$attrs = $this->getSeparateAttributes();
		return (isset($attrs[$id]) ? $attrs[$id] : null);
	}

	public function getEavAttributeKeys () {
		if ( !$this->eavAttributes ) {
			return $this->eavAttributes = $this->category->attrs(array('condition' => 'attrs.common = 1'));
		}
		return $this->eavAttributes;
	}

	public function searchWithText ( $search = '' ) {
		if ( $search ) {
			$criteria = new CDbCriteria();
			$criteria->with = 'torrents';
			$this->getDbCriteria()->mergeWith($criteria);

			$this->withEavAttributes(array($search));
		}
	}

	public function searchWithTags ( $tags = '' ) {
		if ( $tags ) {
			$this->taggedWith($tags);
		}
	}

	public function searchWithCategory ( $category = '' ) {
		if ( $category ) {
			$criteria = new CDbCriteria();
			$criteria->with = 'category';
			$criteria->compare('category.name', $category);
			$this->getDbCriteria()->mergeWith($criteria);
		}
	}
}