<?php

/**
 * @property integer $uId
 * @property string  $modelName
 * @property string  $data
 */
class Savedsearch extends EActiveRecord {
	public $cacheTime = 3600;

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'savedSearches';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'uId, modelName, data',
				'required'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations () {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return CMap::mergeArray(parent::relations(), array());
	}

	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			if ( !class_exists(self::classNameToNamespace($this->modelName)) ) {
				$this->addError('modelName', Yii::t('savedsearchesModule.common', 'Модель не существует'));
				return false;
			}

			return true;
		}
		return false;
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			$this->data = serialize($this->data);

			return true;
		}
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array();
	}

	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	public function getUid () {
		return $this->uId;
	}

	/**
	 * @return array
	 */
	public function getData () {
		if ( $data = @unserialize($this->data) ) {
			return $data;
		}
		return array();
	}

	public static function getDatePeriods () {
		return array(
			'allTime' => Yii::t('savedsearchesModule.common', 'Все время'),
			'year'    => Yii::t('savedsearchesModule.common', 'Год'),
			'month'   => Yii::t('savedsearchesModule.common', 'Месяц'),
			'week'    => Yii::t('savedsearchesModule.common', 'Неделя'),
			'day'     => Yii::t('savedsearchesModule.common', 'День'),
		);
	}
}
