<?php

/**
 * This is the model class for table "files".
 *
 * The followings are the available columns in table 'files':
 * @property integer $id
 * @property string  $title
 * @property string  $originalTitle
 * @property string  $extension
 * @property string  $description
 * @property integer $ownerId
 * @property string  $modelName
 * @property integer $modelId
 * @property integer $ctime
 */
class File extends CActiveRecord {

	public $file;

	const STATE_NAME = '__holdedFiles';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return File the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'files';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(

			array(
				'file, description',
				'required',
				'on' => 'createStory'
			),
			array(
				'description',
				'required',
				'on' => 'updateStory'
			),
			array(
				'file',
				'file',
				'allowEmpty' => true,
				'types'      => 'jpg,jpeg,gif,png',
				'on'         => 'createStory, updateStory'
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, title, extension, description, ownerId, modelName, modelId',
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
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'          => 'ID',
			'title'       => 'Title',
			'extension'   => 'Extension',
			'description' => 'Description',
			'ownerId'     => 'Owner',
			'modelName'   => 'Model Name',
			'modelId'     => 'Model',
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
		$criteria->compare('extension', $this->extension, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('ownerId', $this->ownerId);
		$criteria->compare('modelName', $this->modelName, true);
		$criteria->compare('modelId', $this->modelId);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->file instanceof CUploadedFile && !empty($this->file->name) ) {
				/* @var $current File */

				$current = self::findByPk($this->getId());
				if ( $current ) {
					$this->deleteFile($current->getFilePath(true));
				}

				$this->title = md5($this->originalTitle . time());
				if ( ($pos = strrpos($this->originalTitle, '.')) !== false ) {
					$this->originalTitle = (string) substr($this->originalTitle, 0, $pos);
				}
				$this->originalTitle = $this->file->name;
				$this->extension = $this->file->extensionName;
			}

			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->ownerId = Yii::app()->getUser()->getId();
			}

			return true;
		}
	}

	protected function afterSave () {
		parent::afterSave();

		if ( $this->file instanceof CUploadedFile ) {

			$this->file->saveAs($this->getFilePath(true));

			if ( !$this->modelId ) {
				$newFiles = array(
					'modelName' => $this->modelName,
					'title'     => $this->getTitle(),
				);

				if ( !$_files = Yii::app()->getUser()->getState(self::STATE_NAME) ) {
					$_files = array();
				}
				$_files[] = $newFiles;

				Yii::app()->getUser()->setState(self::STATE_NAME, $_files);
			}
		}
	}

	protected function afterDelete () {
		parent::afterDelete();

		if ( !$this->deleteFile() ) {
			throw new CException('Cant delete file');
		}
	}

	public function deleteFile ( $file = false ) {
		if ( !$file ) {
			$file = $this->getFilePath(true);
		}
		return unlink($file);
	}

	public function getId () {
		return $this->id;
	}

	public function getFilePath ( $full = false ) {
		$md5 = md5($this->originalTitle);

		$path = 'uploads/files/' . substr($md5, 0, 2) . '/';
		if ( !is_dir($path) ) {
			mkdir($path, 0777, true);
		}

		if ( $full ) {
			$path .= $this->getTitle() . '.' . $this->getExt();
		}

		return $path;
	}

	public function getFileUrl () {
		return Yii::app()->getBaseUrl() . '/' . $this->getFilePath(true);
	}

	public function getTitle () {
		return $this->title;
	}

	public function getExt () {
		return $this->extension;
	}
}