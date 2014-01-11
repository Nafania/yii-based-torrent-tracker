<?php

/**
 * This is the model class for table "groupUsers".
 *
 * The followings are the available columns in table 'groupUsers':
 * @property integer $idGroup
 * @property integer $idUser
 * @property integer $status
 * @property integer $ctime
 * @property Group   $group
 */
class GroupUser extends EActiveRecord {
	const STATUS_DECLINED = 1;
	const STATUS_APPROVED = 2;
	const STATUS_NEW = 0;
	const STATUS_INVITED = 3;
	const STATUS_INVITE_DECLINED = 4;

	protected $userName;

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return GroupUser the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'groupUsers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'idUser',
				'required'
			),
			array(
				'idUser',
				'exist',
				'allowEmpty'    => false,
				'attributeName' => 'id',
				'className'     => 'User',
				'message'       => Yii::t('groupsModule.common', 'Указан неверный пользователь'),
			),
			array(
				'idGroup',
				'exist',
				'allowEmpty'    => true,
				'attributeName' => 'id',
				'className'     => 'Group',
				'message'       => Yii::t('groupsModule.common', 'Указана неверная группа'),
			),

			array(
				'status',
				'in',
				'range' => array_keys($this->statusLabels()),
			),
			array(
				'status',
				'checkStatus',
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'idGroup, idUser',
				'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'idGroup' => 'Id Group',
			'idUser'  => 'Id User',
		);
	}

	public function scopes () {
		return array(
			'new'      => array(
				'condition' => 'status = :status',
				'params'    => array(
					':status' => self::STATUS_NEW,
				)
			),
			'declined' => array(
				'condition' => 'status = :status',
				'params'    => array(
					':status' => self::STATUS_DECLINED,
				)
			),
			'approved' => array(
				'condition' => 'status = :status',
				'params'    => array(
					':status' => self::STATUS_APPROVED,
				)
			),
			'invited'  => array(
				'condition' => 'status = :status',
				'params'    => array(
					':status' => self::STATUS_INVITED,
				)
			),
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

		$criteria->compare('idGroup', $this->idGroup);
		$criteria->compare('idUser', $this->idUser);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}


	protected function beforeValidate () {
		if ( parent::beforeValidate() && $this->user ) {
			$validator = CValidator::createValidator('unique',
				$this,
				'idUser',
				array(
					'criteria' => array(
						'condition' => 'idGroup = :idGroup',
						'params'    => array(
							':idGroup' => $this->idGroup,
						),
					),
					'message'  => Yii::t('GroupsModule.common',
							'Пользователь {userName} уже подавал заявку на вступление в эту группу.',
							array('{userName}' => $this->user->getName()))
				));
			$this->getValidatorList()->insertAt(0, $validator);
		}
		return true;
	}

	public function checkStatus ( $attribute, $params ) {
		$model = self::findByAttributes(array(
			'idUser'  => $this->idUser,
			'idGroup' => $this->idGroup
		));

		/**
		 * Если предыдущий статус был Приглашен, а теперь становится одобрен или отклонен инвайт, то
		 * это может сделать только тот, кто приглашение получил.
		 * Владелец же группы не может менять статусы приглашенных участников.
		 */
		if ( $model->status == self::STATUS_INVITED && ($this->status == self::STATUS_APPROVED || $this->status == self::STATUS_INVITE_DECLINED) && $this->idUser != Yii::app()->getUser()->getId() ) {
			$this->addError($attribute,
				Yii::t('groupsModule.common', 'Вы можете менять статус приглашения только для себя.'));
		}
	}

	public function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				if ( $this->status === null ) {
					$this->status = self::STATUS_NEW;
				}
			}

			return true;
		}
	}

	public function statusLabels () {
		return array(
			self::STATUS_APPROVED        => 'Участник',
			self::STATUS_DECLINED        => 'Отклонен',
			self::STATUS_NEW             => 'Не проверен',
			self::STATUS_INVITED         => 'Приглашен',
			self::STATUS_INVITE_DECLINED => 'Приглашение отклонено',
		);
	}

	public function getStatusLabel () {
		$statusLabels = $this->statusLabels();
		return (isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '');
	}

	public function getUser () {
		if ( $user = $this->getRelated('user') ) {
			return $user;
		}
		elseif ( $this->idUser ) {
			return User::model()->findByPk($this->idUser);
		}
		else {
			return false;
		}
	}
}