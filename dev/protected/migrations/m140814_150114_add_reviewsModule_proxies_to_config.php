<?php

class m140814_150114_add_reviewsModule_proxies_to_config extends CDbMigration
{
    public function safeUp()
    {
        Yii::app()->config->add([
            'param' => 'reviewsModule.proxies',
            'value' => [
                '93.115.8.229:8089',
                '119.46.110.17:8080',
                '190.151.10.226:8080',
            ],
            'type' => 'array'
        ]);
    }

    public function safeDown()
    {
        Yii::app()->config->delete('reviewsModule.proxies');
    }
}