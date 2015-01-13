<?php
namespace modules\torrents\models;

use CDbCriteria;
use CActiveDataProvider;
use CMap;
use Yii;
use modules\torrents\components AS tComponents;
use modules\torrents\models AS models;

/**
 * This is the model class for table "torrentGroups".
 *
 * The followings are the available columns in table 'torrentGroups':
 * @property integer                               $id
 * @property string                                $title
 * @property integer                               $ctime
 * @property string                                $picture
 * @property integer                               $mtime
 * @property integer                               $cId
 * @property \Category                             $category
 * @property integer                               $uid
 * @property models\Torrent[]                      torrents
 * @property string                                description
 * @mixin \EEavBehavior
 * @mixin \GetTorrentTitleBehavior
 * @mixin \TorrentNameRuleBehavior
 * @mixin \FavoritesBehavior
 *
 */
class TorrentGroup extends \EActiveRecord implements \ChangesInterface, \WebInterface {
	private $eavAttributes;

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return \modules\torrents\models\TorrentGroup the static model class
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
					'title',
					'length',
					'max' => 255
				),
				array(
					'picture',
					'required',
					'on' => 'insert'
				),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array(
					'id, title, ctime, picture, mtime',
					'safe',
					'on' => 'search'
				),
				array(
					'id, title, ctime, picture, mtime, cId',
					'safe',
					'on' => 'adminSearch'
				),
			));
	}

	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array(
				'torrents' => array(
					self::HAS_MANY,
					'modules\torrents\models\Torrent',
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
					'cacheId'          => null,
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
				'delete187F3' => array(
					'class' => 'application.modules.torrents.behaviors.Delete187F3'
				)
			),
			array(
				'getTorrentTitleBehavior' => array(
					'class' => 'application.modules.torrents.behaviors.GetTorrentTitleBehavior'
				)
			),
			array(
				'SlugBehavior' => array(
					'class'         => 'application.extensions.SlugBehavior.aii.behaviors.SlugBehavior',
					'sourceMethod'  => 'getTitle',
					'slugAttribute' => 'slug',
					'mode'          => 'translit',
				),
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'            => Yii::t('torrentsModule.common', 'Id'),
			'title'         => Yii::t('torrentsModule.common', 'Название'),
			'ctime'         => Yii::t('torrentsModule.common', 'Время создания'),
			'cId'           => Yii::t('torrentsModule.common', 'Категория'),
			'picture'       => Yii::t('torrentsModule.common', 'Изображение'),
			'mtime'         => Yii::t('torrentsModule.common', 'Время'),
			'rating'        => Yii::t('torrentsModule.common', 'Рейтинг'),
			'commentsCount' => Yii::t('torrentsModule.common', 'Кол-во комментариев'),
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
		$criteria->compare('t.title', $this->title, true);
		$criteria->compare('t.ctime', $this->ctime);
		$criteria->compare('t.mtime', $this->mtime);
		$criteria->compare('t.cId', $this->cId);

		$sort = new \CSort();
		$sort->sortVar = 'sort';
		$sort->attributes = array(
			'rating'        => 'rating.rating',
			'commentsCount' => 'commentsCount.count',
			'*'
		);

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageVar'  => 'page',
				'pageSize' => Yii::app()->user->getState('pageSize', Yii::app()->params['defaultPageSize']),
			),
			'sort'       => $sort,
		));
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			$this->description = null;

			if ( $this->getIsNewRecord() ) {
				$this->ctime = $this->mtime = time();
				$this->uid = Yii::app()->getUser()->getId();
			}

			return true;
		}

		return false;
	}

	public static function getSortColums () {
		return array(
			'mtime.desc'         => Yii::t('torrentsModule.common', 'Время'),
			'rating.desc'        => Yii::t('torrentsModule.common', 'Рейтинг'),
			'commentsCount.desc' => Yii::t('torrentsModule.common', 'Кол-во комментариев'),
		);
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
		if ( $this->description !== null ) {
			return $this->description;
		}
		//TODO: get proper description and more fast
		$attributes = $this->getEavAttributeKeys();
		foreach ( $attributes AS $attr ) {
			if ( $attr->type == \Attribute::TYPE_TEXTAREA ) {
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

		$return = array();
		foreach ( $attrs AS $id => $val ) {
			$attribute = $attributes[$id];
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
		if (trim($search)) {
			$criteria = new CDbCriteria();
			$alias = $this->getTableAlias();
			try {
                $rows = Yii::app()->sphinx->createCommand('SELECT id FROM yiiTorrents WHERE MATCH(:term)')->queryAll(true, [':term' => '@title ' . \SphinxHelper::escapeMatch($search)]);

                $keys = [];
                foreach ( $rows AS $row ) {
                    $keys[] = $row['id'];
                }

				$criteria->addInCondition($alias . '.id', $keys);

			} catch ( \CException $e ) {
				$criteria = new CDbCriteria();
				$criteria->condition = $alias . '.title LIKE :search';
				$criteria->params[':search'] = '%' . $search . '%';
			}
			$this->getDbCriteria()->mergeWith($criteria);
		}
	}

	public function searchWithTags ( $tags = '' ) {
		if ( $tags ) {
			$this->taggedWith($tags);
		}
	}

	public function searchWithNotTags ( $tags = '' ) {
		if ( $tags ) {
			$this->notTaggedWith($tags);
		}
	}

	public function searchWithCategory ( $category = '' ) {
		if ( $category ) {
			$criteria = new CDbCriteria();
			$criteria->with = 'category';
			if ( is_numeric($category) ) {
				$criteria->compare('category.id', $category);
			}
			else {
				$criteria->compare('category.name', $category);
			}
			$this->getDbCriteria()->mergeWith($criteria);
		}
	}

	public function getChangesText () {
		return Yii::t('torrentsModule.common',
			'В группу "{groupName}" добавлен новый торрент',
			array('{groupName}' => $this->getTitle()));
	}

	public function getChangesTitle () {
		return Yii::t('torrentsModule.common', 'Добавлен новый торрент');
	}

	public function getMtime () {
		return $this->mtime;
	}

	public function getChangesIcon () {
		return 'download';
	}

	public function getDownloadsCount () {
		$count = 0;
		foreach ( $this->torrents AS $torrent ) {
			$count += $torrent->getDownloads();
		}

		return $count;
	}

    public function getTitle () {
        return $this->title;
    }


	public function getPeriodCriteria ( $period, $alias = '' ) {
		$criteria = new CDbCriteria();

		if ( !$alias ) {
			$alias = $this->getTableAlias();
		}

		switch ( $period ) {
			case 'day':
				$criteria->addCondition($alias . '.mtime BETWEEN :start AND :end');
				$criteria->params[':start'] = time() - 24 * 60 * 60;
				$criteria->params[':end'] = time();
				break;
			case 'week':
				$criteria->addCondition($alias . '.mtime BETWEEN :start AND :end');
				$criteria->params[':start'] = time() - 7 * 24 * 60 * 60;
				$criteria->params[':end'] = time();
				break;
			case 'month':
				$criteria->addCondition($alias . '.mtime BETWEEN :start AND :end');
				$criteria->params[':start'] = time() - 30 * 24 * 60 * 60;
				$criteria->params[':end'] = time();
				break;
			case 'year':
				$criteria->addCondition($alias . '.mtime BETWEEN :start AND :end');
				$criteria->params[':start'] = time() - 365 * 24 * 60 * 60;
				$criteria->params[':end'] = time();
				break;
			default:
			case 'allTime':
				break;
		}

		return $criteria;
	}

    public function getPluralNames () {
        return [
            Yii::t('torrentsModule.common', 'Группа торрентов'),
            Yii::t('torrentsModule.common', 'Группы торрентов'),
        ];
    }
}