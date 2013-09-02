<?php

/**
 * This is the model class for table "subscriptions".
 *
 * The followings are the available columns in table 'subscriptions':
 * @property integer        $modelId
 * @property integer        $modelName
 * @property integer        $uId
 * @property integer        $ctime
 */
class Subscription extends EActiveRecord {

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
		return 'subscriptions';
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
				     'modelId, modelName',
				     'required'
			     ),
			     array(
				     'modelId',
				     'exists',
				     'className' => $this->modelName
			     )
			));
	}

	public function behaviors () {
		return CMap::mergeArray(parent::behaviors(),
			array());
	}

	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array());
	}


	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {

			return true;
		}
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'ctime'     => Yii::t('subscriptionsModule.common', 'Добавлено'),
			'modelId'   => Yii::t('subscriptionsModule.common', 'Model Id'),
			'modelName' => Yii::t('subscriptionsModule.common', 'Model Name'),
			'uId'       => Yii::t('subscriptionsModule.common', 'User id'),
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

		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('modelName', $this->modelName);
		$criteria->compare('uId', $this->uId);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {

			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->uId = Yii::app()->getUser()->getId();
			}

			return true;
		}
	}

	public function primaryKey () {
		return array(
			'modelId',
			'modelName',
			'uId'
		);
	}

	public static function check ( $model ) {
		if ( Yii::app()->getUser()->getIsGuest() ) {
			return false;
		}
		return self::model()->findByPk(array('modelId'  => $model->getPrimaryKey(),
		                                    'modelName' => get_class($model),
		                                    'uId'       => Yii::app()->getUser()->getId()
		                               ));
	}
}