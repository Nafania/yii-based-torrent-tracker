<?php

/**
 * This is the model class for table "reports".
 *
 * The followings are the available columns in table 'reports':
 * @property integer $id
 * @property string  $modelName
 * @property integer $modelId
 * @property integer $state
 */
class Report extends EActiveRecord {

	const REPORT_STATE_NEW = 0;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Report the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'reports';
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
					'modelName, modelId',
					'required'
				),
				array(
					'modelId, state',
					'numerical',
					'integerOnly' => true
				),
				array(
					'modelName',
					'length',
					'max' => 255
				),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array(
					'id, modelName, modelId,  state',
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
				'contents' => array(
					self::HAS_MANY,
					'ReportContent',
					'rId'
				)
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'        => 'ID',
			'modelName' => 'Model Name',
			'modelId'   => 'Model',
			'state'     => 'State',
		);
	}

	public function stateLabels () {
		return array(
			self::REPORT_STATE_NEW => Yii::t('reportsModule.common', 'Новая'),
		);
	}


	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			$validator = CValidator::createValidator('exist',
				$this,
				'modelId',
				array(
				     'attributeName' => 'id',
				     'className'     => self::classNameToNamespace($this->modelName),
				     'allowEmpty'    => false,
				));
			$this->getValidatorList()->insertAt(0, $validator);

			return true;
		}
		return false;
	}


	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->state = self::REPORT_STATE_NEW;
			}

			return true;
		}
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
		$criteria->compare('modelName', $this->modelName, true);
		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('state', $this->state);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public function getId () {
		return $this->id;
	}

	public function getStateLabel () {
		$labels = $this->stateLabels();
		return (isset($labels[$this->state]) ? $labels[$this->state] : null);
	}

	public function getUrl () {
		if ( $this->getIsNewRecord() ) {
			return null;
		}
		$modelName = $this->classNameToNamespace($this->modelName);
		$model = $modelName::model()->findByPk($this->modelId);

		if ( $model ) {
			return $model->getUrl();
		}
		return null;
	}
}