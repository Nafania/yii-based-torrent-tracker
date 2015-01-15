<?php

class AutotagsModule extends CWebModule
{

    public $backendController = 'autotagsBackend';

    public function init()
    {
        $this->setImport(
            [
                'application.modules.autotags.models.ar.AutoTag'
            ]
        );
    }

    public static function register()
    {
        self::_addUrlRules();

        Yii::app()->pd->addAdminModule('autotags', 'category management');

        Yii::app()->pd->registerBehavior(
            'modules\torrents\models\Torrent',
            [
                'autoTagBehavior' => [
                    'class' => 'modules\autotags\behaviors\AutoTag'
                ]
            ]
        );
    }

    private static function _addUrlRules()
    {
        Yii::app()->pd->addUrlRules(
            [
                'yiiadmin/autotags/backend/<action:\w+>/*' => 'autotags/autotagsBackend/<action>',
                'yiiadmin/autotags/backend/*' => 'autotags/autotagsBackend',
            ],
            false
        );
    }

    public function install()
    {
        Yii::import('application.modules.autotags.migrations.install');

        $Install = new install();
        $Install->safeUp();
    }

    public function uninstall()
    {
        Yii::import('application.modules.autotags.migrations.install');

        $Install = new install();
        $Install->safeDown();
    }
}