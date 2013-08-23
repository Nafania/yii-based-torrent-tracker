<?php

/**
 * This is the model class for table "torrentCommentsRelations".
 *
 * The followings are the available columns in table 'torrentCommentsRelations':
 * @property integer $commentId
 * @property integer $torrentId
 */
class TorrentCommentsRelations extends EActiveRecord
{
	public $cacheTime = 3600;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TorrentCommentsRelations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'torrentCommentsRelations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('commentId, torrentId', 'required'),
			array('commentId, torrentId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('commentId, torrentId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'commentId' => 'Comment',
			'torrentId' => 'Torrent',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('commentId',$this->commentId);
		$criteria->compare('torrentId',$this->torrentId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}