<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 15.03.14
 * Time: 20:14
 */
namespace modules\torrents\models;

class TorrentstreamCategory extends \EActiveRecord
{
    public $cacheTime = 3600;
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     *
     * @return Torrent the static model class
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
        return 'torrentstream_categories';
    }

    public function getTitle () {
        return $this->title;
    }
}
