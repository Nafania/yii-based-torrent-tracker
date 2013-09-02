<?php

/**
 * This is the model class for table "comments".
 *
 * The followings are the available columns in table 'comments':
 * @property integer $id
 * @property string  $text
 * @property integer $ownerId
 * @property integer $ctime
 * @property integer $mtime
 * @property integer $status
 * @property integer $parentId
 * @property string  $modelName
 * @property integer $modelId
 */
class Comment extends EActiveRecord implements ChangesInterface {

	const APPROVED = 0;

	public $childs;

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Comment the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		//TODO реализовать валидацию данных модели, корректность id и типа модели
		return CMap::mergeArray(parent::rules(),
			array(
			     array(
				     'text, modelId, modelName',
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
				     'modelId, parentId',
				     'numerical',
				     'integerOnly' => true
			     ),
			     array(
				     'modelName',
				     'length',
				     'max' => 45
			     ),
			     // The following rule is used by search().
			     // Please remove those attributes that should not be searched.
			     array(
				     'id, text, ownerId, ctime, mtime, status, parentId, modelName, modelId',
				     'safe',
				     'on' => 'search'
			     ),
			));
	}

	public function behaviors() {
		return CMap::mergeArray(parent::behaviors(), array(
		                                                'AdjacencyListBehavior' => array(
			                                                'class' => 'application.modules.comments.behaviors.AdjacencyListBehavior',
			                                                'parentAttribute' => 'parentId',
		                                                )
		                                             ));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'        => 'ID',
			'text'      => 'Text',
			'ownerId'   => 'Owner',
			'ctime'     => 'Ctime',
			'mtime'     => 'Mtime',
			'status'    => 'Status',
			'parentId'  => 'Parent',
			'modelName' => 'Model Name',
			'modelId'   => 'Model',
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
		$criteria->compare('text', $this->text, true);
		$criteria->compare('ownerId', $this->ownerId);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('mtime', $this->mtime);
		$criteria->compare('status', $this->status);
		$criteria->compare('parentId', $this->parentId);
		$criteria->compare('modelName', $this->modelName, true);
		$criteria->compare('modelId', $this->modelId);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( defined('IN_CONVERT') ) {
				return true;
			}
			if ( $this->getIsNewRecord() ) {
				$this->ownerId = Yii::app()->getUser()->getId();
				$this->ctime = time();
				$this->status = self::APPROVED;
			}

			return true;
		}
	}

	public function defaultScope () {
		return array(
			'order' => 't.parentId ASC, t.ctime ASC'
		);
	}

	/*
	* recursively build the comment tree for given root node
	* @param array $data array with comments data
	* @int $rootID root node id
	* @return Comment array
	*/

	public static function buildTree ( &$data, $rootID = 0 ) {
		$tree = array();
		foreach ( $data as $id => $node ) {
			$node->parentId = $node->parentId === null ? 0 : $node->parentId;
			if ( $node->parentId == $rootID ) {
				unset($data[$id]);
				$node->childs = self::buildTree($data, $node->id);
				$tree[] = $node;
			}
		}
		return $tree;
	}

	public function getId () {
		return $this->id;
	}

	public function getText () {
		return $this->text;
	}

	public function getTitle () {
		return $this->id;
	}

	public function getOwner () {
		return $this->user;
	}

	public function getParentId () {
		return $this->parentId;
	}

	public function getChangesText () {
		return Yii::t('commentsModule.common', 'Добавлен ответ на ваш комментарий');
	}

	public function getChangesTitle (){
		return Yii::t('commentsModule.common', 'Ответ на ваш комментарий');
	}

	public function getMtime () {
		return $this->mtime;
	}

	public function getUrl () {
		$modelName = $this->modelName;
		$owner = $modelName::model()->findByPk($this->modelId);
		return CMap::mergeArray($owner->getUrl(), array('#' => 'comment-' . $this->getId()));
	}

	public function getChangesIcon () {
		return 'comment';
	}
}