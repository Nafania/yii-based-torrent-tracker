<?php

/**
 * This is the model class for table "ratingRelations".
 *
 * The followings are the available columns in table 'ratingRelations':
 * @property string  $modelName
 * @property integer $modelId
 * @property integer $rating
 * @property integer $uid
 * @property integer $ctime
 * @property integer state
 */
class RatingRelations extends EActiveRecord {
	const RATING_STATE_PLUS = 1;
	const RATING_STATE_MINUS = 0;

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return RatingRelations the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'ratingRelations';
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
				     'modelName, modelId, rating',
				     'required'
			     ),
			     array(
				     'modelId, rating',
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
				     'modelName, modelId, rating',
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
			'modelName' => 'Model Name',
			'modelId'   => 'Model',
			'rating'    => 'Rating',
		);
	}

	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {

			$this->uId = Yii::app()->getUser()->getId();

			$validator = CValidator::createValidator('unique',
				$this,
				'modelName',
				array(
				     'criteria' => array(
					     'condition' => 'modelId=:modelId AND uId = :uId',
					     'params'    => array(
						     ':modelId' => $this->modelId,
						     ':uId' => $this->uId,
					     )
				     )
				));
			$this->getValidatorList()->insertAt(0, $validator);

			$modelName = $this->modelName;
			if ( method_exists($modelName, 'getOwner') ) {
				if ( $modelName::model()->findByPk($this->modelId)->getOwner()->getId() == $this->uId ) {
					$this->addError('uid', Yii::t('commentsModule.common', 'Cant create own rating'));
					return false;
				}
			}

			return true;
		}
		return false;
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
			}

			return true;
		}
	}

	protected function afterSave () {
		$Rating = Rating::model()->findByPk(array(
		                                         'modelName' => $this->modelName,
		                                         'modelId'   => $this->modelId
		                                    ));

		if ( $Rating ) {
			$Rating->saveCounters(array('rating' => ($this->state == self::RATING_STATE_PLUS ? 1 : -1)));
		}
		else {
			$Rating = new Rating();
			$Rating->modelName = $this->modelName;
			$Rating->modelId = $this->modelId;
			$Rating->rating = ($this->state == self::RATING_STATE_PLUS ? 1 : -1);
			$Rating->save();
		}

		$modelName = $this->modelName;
		if ( method_exists($modelName, 'getOwner') ) {
			$owner = $modelName::model()->findByPk($this->modelId)->getOwner();

			if ( $owner ) {
				$Rating = Rating::model()->findByPk(array(
				                                         'modelName' => get_class($owner),
				                                         'modelId'   => $owner->getId(),
				                                    ));

				if ( $Rating ) {
					$Rating->saveCounters(array('rating' => ($this->state == self::RATING_STATE_PLUS ? 1 : -1)));
				}
				else {
					$Rating = new Rating();
					$Rating->modelName = get_class($owner);
					$Rating->modelId = $owner->getId();
					$Rating->rating = ($this->state == self::RATING_STATE_PLUS ? 1 : -1);
					$Rating->save();
				}
			}
		}
	}

	public function primaryKey () {
		return array(
			'modelName',
			'modelId',
			'uId'
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

		$criteria->compare('modelName', $this->modelName, true);
		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('rating', $this->rating);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}
}