<?php
/**
 * This is the model class for table "reportsContent".
 *
 * The followings are the available columns in table 'reportsContent':
 * @property integer  $rId
 * @property integer  $uId
 * @property string   $text
 * @property integer  $ctime
 */

class ReportContent extends EActiveRecord {
	public $modelName;
	public $modelId;

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
		return 'reportsContent';
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
				     'text',
				     'required'
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
			     'report' => array(
				     self::BELONGS_TO,
				     'Report',
				     'rId'
			     ),
			     'user'   => array(
				     self::BELONGS_TO,
				     'User',
				     'uId',
			     )
			));
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'rId'   => 'rid',
			'uId'   => 'uid',
			'text'  => Yii::t('reportsModule.common', 'Текст жалобы'),
			'ctime' => 'ctime',
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


			$report = Report::model()->findByAttributes(array(
			                                                 'modelId'   => $this->modelId,
			                                                 'modelName' => $this->modelName
			                                            ));

			if ( $report ) {
				$this->rId = $report->getId();
				$validator = CValidator::createValidator('unique',
					$this,
					'rId',
					array(
					     'criteria' => array(
						     'condition' => 'uId = :uId',
						     'params'    => array(
							     ':uId' => Yii::app()->getUser()->getId(),
						     ),
					     ),
					     'message'  => Yii::t('reportsModule.common', 'Вы уже подавали жалобу на это действие.')
					));
				$this->getValidatorList()->insertAt(0, $validator);
			}

			return true;
		}
		return false;
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->uId = Yii::app()->getUser()->getId();
				$this->ctime = time();

				if ( !$this->rId ) {
					$report = new Report();
					$report->modelName = $this->modelName;
					$report->modelId = $this->modelId;

					if ( $report->save() ) {
						$this->rId = $report->getId();
					}
					else {
						throw new Exception('Cant save report');
					}
				}
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

		$criteria->compare('rId', $this->rId);
		$criteria->compare('uId', $this->uId);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('ctime', $this->ctime);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	public function getText () {
		return $this->text;
	}

	public function getCtime ( $format = false ) {
		if ( $format ) {
			return Yii::app()->getDateFormatter()->formatDateTime($this->ctime);
		}
		return $this->ctime;
	}
}