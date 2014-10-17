<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 17.10.2014
 * Time: 10:12
 */
/**
 * @var DefaultController $this
 * @var CActiveDataProvider $dataProvider
 */

$title = Yii::t('subscriptionsModule.common', 'Мои подписки');
$this->pageTitle = $title;
$this->breadcrumbs[] = $title;


echo '<h1>' . $title . '</h1>';

$this->widget(
    'bootstrap.widgets.TbGridView',
    [
        'dataProvider' => $dataProvider,
        'type' => 'striped bordered',
        'template' => "{items}\n{pager}",
        'enableHistory' => true,
        'enableSorting' => true,
        'id' => 'subscription-grid',
        'columns' => [
            [
                'header' => Yii::t('subscriptionsModule.common', 'Тип'),
                'name' => 'modelName',
                'value' => function( Subscription $model ) {
                    $subscriptionModel = $model->getSubscriptionModelInstance();

                    if ( $subscriptionModel instanceof WebInterface ) {
                        return $subscriptionModel->getPluralNames()[0];
                    }
                    else {
                        return '---';
                    }
                },
            ],
            [
                'name' => 'modelName',
                'value' => function( Subscription $model ) {
                    $subscriptionModel = $model->getSubscriptionModelInstance();

                    if ( $subscriptionModel instanceof WebInterface && $model = $subscriptionModel->findByPk($model->modelId) ) {
                        return CHtml::link($model->getTitle(), $model->getUrl());
                    }
                    else {
                        return '---';
                    }
                },
                'type' => 'html'
            ],
            [
                'name' => 'ctime',
                'type'=> 'datetime'
            ]
        ],
    ]
);