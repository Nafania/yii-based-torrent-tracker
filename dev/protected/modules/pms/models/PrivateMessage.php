<?php

/**
 * This is the model class for table "privateMessages".
 *
 * The followings are the available columns in table 'privateMessages':
 * @property integer          $id
 * @property string           $senderUid
 * @property string           $receiverUid
 * @property string           $subject
 * @property string           $message
 * @property integer          $readed
 * @property integer          $branch
 * @property integer          $ctime
 * @property integer          $parentId
 * @property integer          $deletedBy
 *
 * The followings are the available model relations:
 * @property User             $sender
 * @property User             $receiver
 * @property PrivateMessage[] $privateMessages
 * @property User             $user
 */
class PrivateMessage extends EActiveRecord {

	const UNREADED = 0;
	const READED = 1;

	public $childs;

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'privateMessages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return CMap::mergeArray(parent::rules(),
			array(
			     array(
				     'subject, message, receiverUid',
				     'required'
			     ),
			     array(
				     'subject',
				     'length',
				     'max' => 255
			     ),
			     array(
				     'receiverUid',
				     'exist',
				     'className'     => 'User',
				     'attributeName' => 'id',
				     'allowEmpty'    => false,
			     ),
			     array(
				     'receiverUid',
				     'compare',
				     'compareValue' => Yii::app()->getUser()->getId(),
				     'operator'     => '!=',
				     'message'      => Yii::t('pmsModule.common', 'Вы не можете отправить сообщение самому себе.')
			     ),
			     array(
				     'message',
				     'filter',
				     'filter' => array(
					     new CHtmlPurifier(),
					     'purify'
				     )
			     ),
			     array(
				     'parentId',
				     'safe'
			     ),
			     // The following rule is used by search().
			     // @todo Please remove those attributes that should not be searched.
			     array(
				     'id, senderUid, receiverUid, subject, message, readed, branch, ctime',
				     'safe',
				     'on' => 'search'
			     ),
			));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations () {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return CMap::mergeArray(parent::relations(),
			array(
			     'sender'          => array(
				     self::BELONGS_TO,
				     'User',
				     'senderUid'
			     ),
			     'receiver'        => array(
				     self::BELONGS_TO,
				     'User',
				     'receiverUid'
			     ),
			     'branch'          => array(
				     self::BELONGS_TO,
				     'PrivateMessage',
				     'branch'
			     ),
			     'privateMessages' => array(
				     self::HAS_MANY,
				     'PrivateMessage',
				     'branch'
			     ),
			));
	}


	public function behaviors () {
		return CMap::mergeArray(parent::behaviors(),
			array( /*'AdjacencyListBehavior' => array(
				     'class'           => 'application.modules.pms.behaviors.AdjacencyListBehavior',
				     'parentAttribute' => 'parentId',
			     )*/
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'          => 'ID',
			'senderUid'   => Yii::t('pmsModule.common', 'Отправитель'),
			'receiverUid' => Yii::t('pmsModule.common', 'Получатель'),
			'subject'     => Yii::t('pmsModule.common', 'Тема'),
			'message'     => Yii::t('pmsModule.common', 'Сообщение'),
			'readed'      => Yii::t('pmsModule.common', 'Прочитано'),
			'branch'      => 'Branch',
			'ctime'       => Yii::t('pmsModule.common', 'Дата'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search () {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('senderUid', $this->senderUid);
		$criteria->compare('receiverUid', $this->receiverUid);
		$criteria->compare('subject', $this->subject, true);
		$criteria->compare('message', $this->message, true);
		$criteria->compare('readed ', $this->readed);
		$criteria->compare('branch', $this->branch);
		$criteria->compare('ctime', $this->ctime);

		return new CActiveDataProvider($this, array(
		                                           'criteria'   => $criteria,
		                                           'pagination' => array('pageVar' => 'page'),
		                                      ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return PrivateMessage the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	public function beforeValidate () {
		if ( parent::beforeValidate() ) {
			if ( $this->getIsNewRecord() ) {

				if ( $this->parentId ) {
					$parentModel = self::model()->findByPk($this->parentId);

					if ( !$parentModel ) {
						$this->addError('message', Yii::t('pmsModule.common', 'Неверный id родительского сообщения'));
					}
					else {
						$this->subject = $parentModel->subject;
						$this->branch = $parentModel->branch;
						if ( $parentModel->senderUid == Yii::app()->getUser()->getId() ) {
							$this->receiverUid = $parentModel->receiverUid;
						}
						else {
							$this->receiverUid = $parentModel->senderUid;
						}
					}
				}
			}

			return true;
		}
	}

	public function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->senderUid = Yii::app()->getUser()->getId();
				$this->ctime = time();
			}
			else {
				/**
				 * Если кто-то удаляет сообщение
				 */
				if ( $this->deletedBy ) {
					/**
					 * высним старое значение этого поля
					 */
					$model = self::model()->findByPk($this->id);

					/**
					 * если ранее тоже было значение и оно не равно текущему
					 * значит кто-то из ветки сообщений решил его удалить и сейчас
					 * удаляет второй участник.
					 * Так как оба удаляют сообщени, значит нам нужно удалить это сообщение.
					 */
					if ( $model->deletedBy && $model->deletedBy <> $this->deletedBy ) {
						$this->delete();
					}
				}
			}

			return true;
		}
	}

	public function scopes () {
		$alias = $this->getTableAlias();

		return array(
			'own'        => array(
				'condition' => "( {$alias}.senderUid = :sender OR {$alias}.receiverUid = :receiver ) AND ( {$alias}.deletedBy != :userId OR {$alias}.deletedBy IS NULL )",
				'params'    => array(
					':receiver' => Yii::app()->getUser()->getId(),
					':sender'   => Yii::app()->getUser()->getId(),
					':userId'   => Yii::app()->getUser()->getId(),
				),
				//'order'     => 'readed DESC, ctime DESC',
			),
			'branches'   => array(
				'select' => "{$alias}.id, senderUid, receiverUid, subject, message, branch, parentId, MAX(ctime) AS ctime, MIN(readed) AS readed",
				'group'  => "{$alias}.branch",
				'order'  => "{$alias}.readed ASC, ctime DESC"
			),
			'viewBranch' => array(
				'order' => "$alias.parentId ASC, $alias.ctime ASC"
			),
		);
	}

	public function getId () {
		return $this->id;
	}

	public function getBranch () {
		return ($this->branch ? $this->branch : $this->id);
	}

	public function getTitle () {
		return $this->subject;
	}

	public function getMessage () {
		return $this->message;
	}

	public function getCtime ( $format = false ) {
		if ( $format ) {
			return date($format, $this->ctime);
		}
		return $this->ctime;
	}

	public function getUrl () {
		return array(
			'/pms/default/view',
			'id' => $this->getBranch()
		);
	}

	public function getUser () {
		return $this->sender;
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

	public static function getAllSenders () {
		$models = self::model()->own()->findAll();

		$ret = array();
		foreach ( $models AS $model ) {
			if ( $model->sender ) {
				$ret[$model->senderUid] = $model->sender->getName();
			}
			else {
				$ret[$model->senderUid] = null;
			}
		}

		asort($ret);

		return $ret;
	}

	public static function getAllReceivers () {
		$models = self::model()->own()->findAll();

		$ret = array();
		foreach ( $models AS $model ) {
			if ( $model->receiver ) {
				$ret[$model->receiverUid] = $model->receiver->getName();
			}
			else {
				$ret[$model->receiverUid] = null;
			}
		}

		asort($ret);

		return $ret;
	}
}
