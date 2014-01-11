<?php

/**
 * This is the model class for table "drafts".
 *
 * The followings are the available columns in table 'drafts':
 * @property string  $formId
 * @property string  $data
 * @property integer $ctime
 * @property integer $mtime
 * @property integer $deleted
 * @property integer $uId
 */
class Draft extends EActiveRecord {
	public $cacheTime = 3600;

	const DELETED = 1;
	const NOT_DELETED = 0;

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'drafts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'formId, data',
				'required'
			),
			array(
				'formId',
				'length',
				'max' => 255
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'formId' => 'Form',
			'data'   => 'Data',
			'ctime'  => 'Ctime',
			'mtime'  => 'Mtime',
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

		$criteria->compare('formId', $this->formId, true);
		$criteria->compare('data', $this->data, true);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('mtime', $this->mtime);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                      ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return Draft the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {
			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->uId = Yii::app()->getUser()->getId();
			}
			$this->mtime = time();

			return true;
		}
		return false;
	}
}
