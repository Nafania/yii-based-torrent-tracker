<?php
namespace modules\torrents\models;
use Yii;
use CDbCriteria;
use CActiveDataProvider;
use CMap;
use modules\torrents\components AS tComponents;
use modules\torrents\models AS models;

/**
 * This is the model class for table "torrents".
 *
 * The followings are the available columns in table 'torrents':
 * @property integer                                 $id
 * @property integer                                 $ctime
 * @property integer                                 $size
 * @property integer                                 $downloads
 * @property integer                                 $seeders
 * @property integer                                 $leechers
 * @property integer                                 $mtime
 * @property string                                  $info_hash
 * @property integer                                 $uid
 * @property string                                  $title
 * @property integer                                 $hashChanged
 * @property models\TorrentGroup                     torrentGroup
 */
class Torrent extends \EActiveRecord {

	const HASH_CHANGED = 1;
	const HASH_NOT_CHANGED = 0;

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Torrent the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'torrents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'info_hash',
				'required',
				'on' => 'insert'
			),
			array(
				'info_hash',
				'unique'
			),
			array(
				'info_hash',
				'file',
				'types'      => 'torrent',
				//'maxSize'    => '204800',
				'allowEmpty' => true
			),
			array(
				'id, ctime, size, downloads, seeders, leechers, mtime',
				'numerical',
				'integerOnly' => true
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, ctime, size, downloads, seeders, leechers, mtime',
				'safe',
				'on' => 'search'
			),
		);
	}

	public function behaviors () {
		return CMap::mergeArray(parent::behaviors(),
			array(
			     'eavAttr' => array(
				     'class'            => 'application.modules.torrents.extensions.eav.EEavBehavior',
				     // Table that stores attributes (required)
				     'tableName'        => 'torrentsEAV',
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
				     'preload'          => true,
			     )
			));
	}

	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array(
			     'torrentGroup' => array(
				     self::BELONGS_TO,
				     'modules\torrents\models\TorrentGroup',
				     'gId'
			     ),
			)

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'        => 'ID',
			'ctime'     => Yii::t('torrentsModule.common', 'Добавлено'),
			'size'      => Yii::t('torrentsModule.common', 'Размер'),
			'downloads' => Yii::t('torrentsModule.common', 'Скачан'),
			'seeders'   => Yii::t('torrentsModule.common', 'Раздают'),
			'leechers'  => Yii::t('torrentsModule.common', 'Качают'),
			'mtime'     => Yii::t('torrentsModule.common', 'Время изменения'),
			'info_hash' => Yii::t('torrentsModule.common', 'Торрент файл'),
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
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('size', $this->size);
		$criteria->compare('downloads', $this->downloads);
		$criteria->compare('seeders', $this->seeders);
		$criteria->compare('leechers', $this->leechers);
		$criteria->compare('mtime', $this->mtime);
		$criteria->order = 'ctime DESC';

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			if ( $this->info_hash instanceof \CUploadedFile && !empty($this->info_hash->name) ) {

				$torrent = new tComponents\TorrentComponent($this->info_hash->getTempName());
				if ( !$torrent->is_torrent($this->info_hash->getTempName()) ) {
					$this->addError('info_hash', Yii::t('torrentsModule.common', 'Not a valid torrent file'));
					return false;
				}
				if ( !$torrent->size() ) {
					$this->addError('info_hash', Yii::t('torrentsModule.common', 'Empty files list in a torrent file'));
					return false;
				}
				if ( self::model()->findAllByAttributes(array('info_hash' => $torrent->hash_info())) ) {
					$this->addError('info_hash', Yii::t('torrentsModule.common', 'Torrent file already exists'));
					return false;
				}
			}

			return true;
		}

		return false;
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			$this->mtime = time();

			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->uid = Yii::app()->getUser()->getId();
			}

			/* @var $current Torrent */
			if ( $this->info_hash instanceof \CUploadedFile && !empty($this->info_hash->name) ) {
				$torrent = new tComponents\TorrentComponent($this->info_hash->getTempName());

				if ( $torrent->is_private() ) {
					$torrent->is_private(false);
				}

				$announceUrl = Yii::app()->config->get('torrentsModule.xbt_listen_url') . ':' . Yii::app()->config->get('torrentsModule.listen_port') . '/announce/';
				$torrent->announce(array($announceUrl));

				$current = self::findByPk($this->getId());
				if ( $current ) {
					$this->deleteXbtHash();
					@unlink($current->getTorrentFilePath() . $this->getId() . '.torrent');
				}
				$this->info_hash->saveAs($this->getTmpFile($torrent->hash_info()));

				$this->info_hash = $torrent->hash_info();
				$this->size = $torrent->size();
			}

			elseif ( !$this->info_hash ) {
				unset($this->info_hash);
			}

			return true;
		}

		return false;
	}

	protected function deleteXbtHash () {
		$this->hashChanged = self::HASH_CHANGED;

		$comm = $this->getDbConnection()->createCommand('INSERT INTO {{xbt_deleted_hashes}} (fid, info_hash) VALUES(:fid, :info_hash)');
		$comm->bindValue(':fid', $this->id);
		$comm->bindValue(':info_hash', $this->info_hash);
		$comm->execute();
	}

	protected function afterSave () {
		if ( file_exists($this->getTmpFile()) ) {
			rename($this->getTmpFile(), $this->getTorrentFilePath() . $this->getId() . '.torrent');
		}

		parent::afterSave();
	}

	protected function afterDelete () {
		parent::afterDelete();

		$this->deleteXbtHash();
	}

	public function getTmpFile ( $hash = false ) {
		if ( $hash ) {
			return Yii::getPathOfAlias('webroot') . '/uploads/tmp/' . md5($hash);
		}
		return Yii::getPathOfAlias('webroot') . '/uploads/tmp/' . md5($this->info_hash);
	}

	public function getTorrentFilePath () {
		$dir = Yii::getPathOfAlias('webroot') . '/uploads/torrents/' . date('Y.m.d',
				$this->ctime) . '/';
		if ( !is_dir($dir) ) {
			@mkdir($dir, 0777, true);
		}
		return $dir;
	}


	public function getId () {
		return $this->id;
	}

	public function getSize ( $nice = false ) {
		if ( $nice ) {
			return \SizeHelper::formatSize($this->size);
		}
		return number_format($this->size);
	}

	public function getCtime ( $format = false ) {
		if ( $format ) {
			return date($format, $this->ctime);
		}
		return $this->ctime;
	}


	public function getEavAttributesWithKeys () {
		$attributes = $this->torrentGroup->category->attrs(array('condition' => 'common=0'));

		$ids = array();
		foreach ( $attributes AS $attribute ) {
			$ids[] = $attribute->getId();
		}

		$attrs = $this->getEavAttributes($ids);

		$return = array();
		foreach ( $attrs AS $id => $val ) {
			$attribute = $attributes[$id];
			if ( !$val || $attribute->separate ) {
				continue;
			}
			$val = nl2br($val);
			if ( $attribute->validator == 'url' ) {
				$val = \TextHelper::makeClickable($val);
			}

			$return[$attribute->getTitle()] = $val;
		}

		return $return;
	}

	public function getSeparateAttribute () {
		if ( $this->title ) {
			return $this->title;
		}
		$sepAttr = $this->torrentGroup->category->attrs(array(
		                                                     'condition' => 'separate = 1',
		                                                ));

		$return = array();
		foreach ( $sepAttr AS $attribute ) {
			$prepend = ($attribute->prepend ? $attribute->prepend . ' ' : '');
			$append = ($attribute->append ? ' ' . $attribute->append : '');

			$return[] = $prepend . $this->getEavAttribute($attribute->id) . $append;
		}

		$return = implode(' - ', $return);

		$this->title = $return;

		$this->save();

		return $return;
	}

	public function getDownloads () {
		return $this->downloads;
	}

	public function getSeeders () {
		return $this->seeders;
	}

	public function getLeechers () {
		return $this->leechers;
	}

	public function getDownloadPath () {
		return Yii::getPathOfAlias('webroot') . '/uploads/torrents/' . date('Y.m.d',
			$this->ctime) . '/' . $this->getId() . '.torrent';
	}

	public function getTitle () {
		return $this->torrentGroup->getTitle() . ' ' . Yii::app()->config->get('torrentsModule.torrentsNameDelimiter') . ' ' . $this->getSeparateAttribute();
	}

	public function getAnnounce () {
		if ( Yii::app()->getUser()->getIsGuest() ) {
			$announce = Yii::app()->config->get('torrentsModule.xbt_listen_url') . ':' . Yii::app()->config->get('torrentsModule.listen_port') . '/announce/';
		}
		else {
			$announce = Yii::app()->config->get('torrentsModule.xbt_listen_url') . ':' . Yii::app()->config->get('torrentsModule.listen_port') . '/' . Yii::app()->getUser()->profile->getTorrentPass() . '/announce/';
		}

		return $announce;
	}
}