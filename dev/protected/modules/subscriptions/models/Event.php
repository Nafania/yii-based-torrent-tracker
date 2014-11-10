<?php

/**
 * This is the model class for table "events".
 *
 * The followings are the available columns in table 'events':
 * @property integer              $id
 * @property string               $title
 * @property string               $text
 * @property string               $url
 * @property integer              $ctime
 * @property integer              $uId
 * @property integer              $unread
 * @property string               $icon
 * @property integer              $notified
 * @property string               $uniqueType
 * @property integer              $count
 */
class Event extends EActiveRecord
{
    const EVENT_UNREAD = 1;
    const EVENT_READED = 0;
    const REDIS_HASH_NAME = 'H:Event';

    public $cacheTime = 3600;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     *
     * @return Event the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'events';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
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

    public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(),
            array());
    }

    public function relations()
    {
        return CMap::mergeArray(parent::relations(),
            array());
    }

    public function defaultScope()
    {
        $alias = $this->getTableAlias(true, false);
        return array(
            'order' => "$alias.ctime DESC"
        );
    }

    public function scopes()
    {
        return array(
            'unreaded' => array(
                'condition' => 'unread = :unread',
                'params' => array(
                    'unread' => self::EVENT_UNREAD
                )
            ),
            'forCurrentUser' => array(
                'condition' => 'uId = :uId',
                'params' => array(
                    'uId' => Yii::app()->getUser()->getId(),
                )
            )
        );
    }



    protected function beforeSave()
    {
        if (parent::beforeSave()) {

            if ($this->getIsNewRecord() && $this->uniqueType) {
                $oldEvent = self::model()->findByAttributes(array(
                    'uniqueType' => $this->uniqueType,
                    'uId' => $this->uId,
                    'unread' => self::EVENT_UNREAD
                ));

                if ($oldEvent) {
                    $oldEvent->saveCounters(array('count' => 1));

                    return false;
                }
            }

            $this->url = serialize($this->url);
            $this->icon = ($this->icon ? $this->icon : 'envelope');

            if ($this->getIsNewRecord()) {
                $this->ctime = time();
                $this->unread = self::EVENT_UNREAD;
                $this->notified = 0;
                $this->count = 1;
            }

            return true;
        }
    }

    protected function afterSave()
    {
        parent::afterSave();

        $redis = \Yii::app()->redis;

        if ($this->unread == self::EVENT_UNREAD) {
            $redis->hIncrBy(self::REDIS_HASH_NAME, $this->uId, 1);
            $redis->publish(md5($this->uId), 'newEvent');
        } else {
            $redis->hIncrBy(self::REDIS_HASH_NAME, $this->uId, -1);
        }
    }

    protected function beforeValidate()
    {
        if (parent::beforeValidate()) {

            return true;
        }

        return false;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getText()
    {
        return $this->text;
    }

    public function getUrl()
    {
        $url = @unserialize($this->url);
        if (!$url) {
            return '';
        }
        return $url;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }
}