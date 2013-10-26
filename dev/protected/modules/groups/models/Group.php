<?php

/**
 * This is the model class for table "groups".
 *
 * The followings are the available columns in table 'groups':
 * @property integer $id
 * @property string  $title
 * @property string  $picture
 * @property integer $type
 * @property string  $description
 * @property integer $ownerId
 * @property integer $blocked
 * @property integer $ctime
 * @property integer $rating
 */
class Group extends EActiveRecord {
	public $cacheTime = 3600;

	const BLOCKED = 1;
	const NOT_BLOCKED = 0;

	const TYPE_OPENED = 0;
	const TYPE_CLOSED = 1;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Group the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'title, type',
				'required'
			),
			array(
				'description',
				'filter',
				'filter' => array(
					new CHtmlPurifier(),
					'purify'
				)
			),
			array(
				'picture',
				'unsafe'
			),
			array(
				'title',
				'length',
				'max' => 125
			),
			array(
				'type',
				'type',
				'type' => 'integer'
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, title, picture, type, description, ownerId, blocked, ctime',
				'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array(
			     'groupUsers'         => array(
				     self::HAS_MANY,
				     'GroupUser',
				     'idGroup'
			     ),
			     'groupUsersCount'    => array(
				     self::STAT,
				     'GroupUser',
				     'idGroup',
				     'condition' => 'status = :status',
				     'params'    => array(
					     'status' => GroupUser::STATUS_APPROVED,
				     ),
			     ),

			     'declinedUsersCount' => array(
				     self::STAT,
				     'GroupUser',
				     'idGroup',
				     'condition' => 'status = :status',
				     'params'    => array(
					     'status' => GroupUser::STATUS_DECLINED,
				     ),
			     ),

			     'newUsersCount'      => array(
				     self::STAT,
				     'GroupUser',
				     'idGroup',
				     'condition' => 'status = :status',
				     'params'    => array(
					     'status' => GroupUser::STATUS_NEW,
				     ),
			     ),

			     'invitedUsersCount'  => array(
				     self::STAT,
				     'GroupUser',
				     'idGroup',
				     'condition' => 'status = :status',
				     'params'    => array(
					     'status' => GroupUser::STATUS_INVITED,
				     ),
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

	public function scopes () {
		return array(
			'open'  => array(
				'condition' => 't.type = ' . self::TYPE_OPENED
			),
			'close' => array(
				'condition' => 't.type = ' . self::TYPE_CLOSED
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'          => 'ID',
			'title'       => Yii::t('groupsModule.common', 'Название'),
			'picture'     => Yii::t('groupsModule.common', 'Картинка'),
			'type'        => Yii::t('groupsModule.common', 'Тип'),
			'description' => Yii::t('groupsModule.common', 'Описание'),
			'ownerId'     => 'Owner',
			'blocked'     => 'Blocked',
			'ctime'       => Yii::t('groupsModule.common', 'Время создания'),
			'rating'      => Yii::t('groupsModule.common', 'Рейтинг'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search () {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$alias = $this->getTableAlias();

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.picture', $this->picture, true);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.ownerId', $this->ownerId);
		$criteria->compare('t.blocked', $this->blocked);
		$criteria->compare('t.ctime', $this->ctime);


		$sort = Yii::app()->getRequest()->getParam('sort');
		/**
		 * TODO: убрать все это в поведения
		 */
		/**
		 * подключаем таблицу рейтингов
		 */
		//if ( strpos($sort, 'rating') !== false ) {
			$criteria->select .= ', r.rating AS rating';
			$criteria->join .= 'LEFT JOIN {{ratings}} r ON ( r.modelName = \'' . get_class($this) . '\' AND r.modelId = t.id)';
		//}

		$sort = new CSort($this);
		$sort->defaultOrder = 'rating DESC';
		$sort->attributes = array(
			'*',
			'rating' => array(
				'asc'  => 'rating ASC',
				'desc' => 'rating DESC',
				'default' => 'desc',
			),
		);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                           'sort'     => $sort
		                                      ));
	}


	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->blocked = self::NOT_BLOCKED;
				$this->ctime = time();
				$this->ownerId = Yii::app()->getUser()->getId();
			}

			return true;
		}
	}

	protected function afterDelete () {
		if ( $this->groupUsers ) {
			foreach ( $this->groupUsers AS $groupUser ) {
				$groupUser->delete();
			}
		}

		return true;
	}

	public function getId () {
		return $this->id;
	}

	public function getTitle ( $encode = true ) {
		return ($encode ? CHtml::encode($this->title) : $this->title);
	}


	public function getCtime ( $format = false ) {
		if ( $format ) {
			return Yii::app()->getDateFormatter()->formatDateTime($this->ctime);
		}
		return $this->ctime;
	}

	public function getTypes () {
		return array(
			self::TYPE_OPENED => Yii::t('GroupsModule.common', 'Открытая группа'),
			self::TYPE_CLOSED => Yii::t('GroupsModule.common', 'Закрытая группа'),
		);
	}

	public function getTypeLabel () {
		$types = $this->getTypes();
		return (isset($types[$this->type]) ? $types[$this->type] : '');
	}

	public function getType () {
		return $this->type;
	}

	public function getUrl () {
		return array(
			'/groups/default/view',
			'id'    => $this->getId(),
			'title' => $this->getSlugTitle()
		);
	}

	public function getDescription () {
		return $this->description;
	}

	public function searchWithText ( $search ) {
		$criteria = new CDbCriteria();
		if ( $search ) {
			$criteria->compare('t.title', $search, true, 'OR');
			$criteria->compare('t.description', $search, true, 'OR');

		}
		$this->getDbCriteria()->mergeWith($criteria);
	}

	public static function checkJoin ( $model ) {
		if ( Yii::app()->getUser()->getIsGuest() || !$model ) {
			return false;
		}

		return GroupUser::model()->findByPk(array(
		                                         'idGroup' => $model->getId(),
		                                         'idUser'  => Yii::app()->getUser()->getId(),
		                                         'status'  => GroupUser::STATUS_APPROVED,
		                                    ));
	}

}