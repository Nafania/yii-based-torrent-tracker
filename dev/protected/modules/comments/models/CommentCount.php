<?php

/**
 * This is the model class for table "commentCounts".
 *
 * The followings are the available columns in table 'commentCounts':
 * @property string $modelName
 * @property integer $modelId
 * @property integer $count
 */
class CommentCount extends EActiveRecord
{
	public $cacheTime = 3600;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'commentCounts';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('modelName, modelId, count', 'required'),
            array('modelId, count', 'numerical', 'integerOnly'=>true),
            array('modelName', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('modelName, modelId, count', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'modelName' => 'Model Name',
            'mode' => 'modelId',
            'count' => 'Count',
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
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('modelName',$this->modelName,true);
        $criteria->compare('modelId',$this->modelId);
        $criteria->compare('count',$this->count);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CommentCount the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

	public function __toString() {
		return ( $this->count ? $this->count : 0);
	}
}