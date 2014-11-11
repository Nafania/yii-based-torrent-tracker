<?php
namespace modules\torrents;

use Yii;
use CActiveRecord;

class TorrentsModule extends \CWebModule
{
    public $controllerNamespace = '\modules\torrents\controllers';

    public $backendController = 'torrentsBackend';
    public $defaultController = 'default';

    private $_assetsUrl;

    public function init()
    {
        $this->setImport(array(
            'modules\torrents\models.*',
            'modules\torrents\components.*',
        ));
    }

    /**
     * @return string the base URL that contains all published asset files.
     */
    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null) {
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.torrents.assets'),
                false,
                -1);
        }
        return $this->_assetsUrl;
    }


    public static function register()
    {
        self::_addUrlRules();
        self::_addModelRules();
        self::_addBehaviors();
        self::_addModelsRelations();
        self::_setImport();

        Yii::app()->pd->addAdminModule('torrents', 'Models management');
    }

    protected static function _addUrlRules()
    {
        Yii::app()->pd->addUrlRules(array(
                'yiiadmin/torrents/backend/<action:\w+>' => 'torrents/torrentsBackend/<action>',
                'yiiadmin/torrents/backend' => 'torrents/torrentsBackend',

                'torrents/<title>-<id>-watch-online' => 'torrents/default/watchOnline',
                'torrents/<title>-<id>' => 'torrents/default/view',
                'torrents/' => 'torrents/default/index',
                'torrents/my' => 'torrents/default/my',
                'torrents/<action:\w+>/*' => 'torrents/default/<action>',
                'torrents/<controller:\w+>/<action:\w+>/*' => 'torrents/<controller>/<action>',
            ),
            false);
    }


    private static function _addModelsRelations()
    {
        Yii::app()->pd->addRelations('User',
            'torrents',
            array(
                CActiveRecord::HAS_MANY,
                'modules\torrents\models\Torrent',
                'uId',
            ),
            'application.modules.torrents.models.*');

        Yii::app()->pd->addRelations('User',
            'torrentsCount',
            array(
                CActiveRecord::STAT,
                'modules\torrents\models\Torrent',
                'uid',
            ),
            'application.modules.torrents.models.*');

        Yii::app()->pd->addRelations('Comment',
            'torrentComments',
            array(
                CActiveRecord::HAS_ONE,
                'modules\torrents\models\TorrentCommentsRelations',
                'commentId',
            ),
            'application.modules.torrents.models.*');

        Yii::app()->pd->addRelations('Comment',
            'torrent',
            array(
                CActiveRecord::HAS_ONE,
                'modules\torrents\models\Torrent',
                'torrentId',
                'through' => 'torrentComments'
            ),
            'application.modules.torrents.models.*');
    }

    private static function _addModelRules()
    {
        Yii::app()->pd->addModelRules('Category',
            array(
                'id',
                'required',
                'on' => 'createTorrent',
            ));

        Yii::app()->pd->addModelRules('Category',
            array(
                'torrentsNameRules',
                'safe',
            ));

        Yii::app()->pd->addModelRules('Comment',
            array(
                'torrentId',
                'safe',
            ));
    }

    private static function _addBehaviors()
    {
        Yii::app()->pd->registerBehavior('Category',
            array(
                'torrentNameRulesBehavior' => array(
                    'class' => 'application.modules.torrents.behaviors.TorrentNameRuleBehavior'
                )
            ));

        Yii::app()->pd->registerBehavior('Comment',
            array(
                'torrentComments' => array(
                    'class' => 'application.modules.torrents.behaviors.TorrentCommentsRelationsBehavior'
                )
            ));
    }

    private static function _setImport()
    {
        Yii::app()->pd->setImport(array('application.modules.torrents.models.*'));
    }


    public function getRss()
    {
        $model = new models\Torrent();

        $model->unsetAttributes(); // clear any default values
        $model->setScenario('search');
        $model->setSearchSettings();

        return $model;
    }
}
