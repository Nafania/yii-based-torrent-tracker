<?php

/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 27.01.14
 * Time: 20:40
 */

Yii::import('bootstrap.widgets.TbButton');

class SubscriptionButton extends TbButton
{
    public $model;

    public $subscribeTitle;

    public $unSubscribeTitle;

    public function init()
    {

        if (!($this->model instanceof EActiveRecord)) {
            throw new CException('Model must be instance of EActiveRecord');
        }

        if (!$this->subscribeTitle) {
            $this->subscribeTitle = Yii::t('subscriptionsModule.common',
                'Следить');
        }

        if (!$this->unSubscribeTitle) {
            $this->unSubscribeTitle = Yii::t('subscriptionsModule.common',
                'Перестать следить');
        }

        Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('subscriptions')->getAssetsUrl() . '/js/subscriptions.js');

        $this->buttonType = 'link';

        if (Subscription::check($this->model->resolveClassName(), $this->model->getPrimaryKey())) {
            $this->icon = 'icon-eye-close';
            $this->label = '';
            $this->url = array('/subscriptions/default/delete');
            $this->htmlOptions = array(
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-model' => $this->model->resolveClassName(),
                'data-id' => $this->model->getId(),
                'data-action' => 'subscription',
                'title' => $this->unSubscribeTitle,
            );
            $this->visible = Yii::app()->getUser()->checkAccess('subscriptions.default.delete');
        } else {
            $this->icon = 'icon-eye-open';
            $this->url = array('/subscriptions/default/create');
            $this->htmlOptions = array(
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-model' => $this->model->resolveClassName(),
                'data-id' => $this->model->getId(),
                'data-action' => 'subscription',
                'title' => $this->subscribeTitle,
            );
            $this->visible = Yii::app()->getUser()->checkAccess('subscriptions.default.create');
        }

        parent::init();
    }
}