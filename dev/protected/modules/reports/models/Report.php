<?php

/**
 * This is the model class for table "reports".
 *
 * The followings are the available columns in table 'reports':
 * @property integer $id
 * @property integer $uId
 * @property string  $modelName
 * @property integer $modelId
 * @property string  $text
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
				     'modelName, modelId, text',
				     'required'
			     ),
			     array(
				     'uId, modelId, state',
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
				     'id, uId, modelName, modelId, text, state',
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
		return CMap::mergeArray(parent::relations(), array());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'        => 'ID',
			'uId'       => 'U',
			'modelName' => 'Model Name',
			'modelId'   => 'Model',
			'text'      => 'Text',
			'state'     => 'State',
		);
	}

	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			$model = self::model()->findByAttributes(array('uId'      => Yii::app()->getUser()->getId(),
			                                              'modelName' => $this->modelName,
			                                              'modelId'   => $this->modelId
			                                         ));
			if ( $model ) {
				$this->addError('text', Yii::t('reportsModule', 'Вы уже подавали жалобу'));
				return false;
			}

			return true;
		}
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->uId = Yii::app()->getUser()->getId();
				$this->ctime = time();
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
		$criteria->compare('uId', $this->uId);
		$criteria->compare('modelName', $this->modelName, true);
		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('state', $this->state);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}
}