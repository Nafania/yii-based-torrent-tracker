<?php

class AnswerWidget extends CWidget
{
    public $model;
    public $modelId;
    public $modelName;
    public $parentId = 0;
    public $torrents = array();

    public function init()
    {
        parent::init();

        if (!Yii::app()->getUser()->getIsGuest()) {

            if (!Yii::app()->getUser()->checkAccess('comments.default.create')) {
                return false;
            }
        }

        Yii::import('application.modules.comments.models.*');

        if (!$this->model instanceof CActiveRecord) {
            throw new CException('Model must be instanceof CActiveRecord');
        }

        $this->modelId = $this->model->getPrimaryKey();
        //$this->modelName = end(explode('\\', get_class($this->model)));
        $this->modelName = $this->model->resolveClassName();

        if (!$this->modelName || !$this->modelId) {
            throw new CException('Not enough data');
        }

        $cs = Yii::app()->getClientScript();
      	$cs->registerScriptFile(Yii::app()->getModule('comments')->getAssetsUrl() . '/imperavi.plugin.quote.js');
    }

    public function run()
    {
        $comment = new Comment();

        $this->render('answer',
            array(
                'comment' => $comment,
                'modelId' => $this->modelId,
                'modelName' => $this->modelName,
                'parentId' => $this->parentId,
                'torrents' => $this->torrents,
                'action' => Yii::app()->createUrl('/comments/default/create')
            ));
    }
}