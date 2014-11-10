<?php
class CommentsTreeWidget extends CWidget {
	public $model;

	public function init () {
		parent::init();

		Yii::import('application.modules.comments.models.*');

		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile(Yii::app()->getModule('comments')->getAssetsUrl() . '/comments.js');
		$cs->registerScript('commentModule',
			'var commentsUrl = ' . CJavaScript::encode(Yii::app()->createUrl('/comments/default/loadAnswerBlock')) . ';',
			CClientScript::POS_HEAD);
		$cs->registerCssFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.css');
		$cs->registerScriptFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.js');
		Yii::app()->getComponent('bootstrap')->registerPackage('loading');
	}

	public function run () {
		$comments = Comment::model()->findAllByAttributes(array(
		                                                       'modelName' => $this->model->resolveClassName(),
		                                                       'modelId'   => $this->model->getPrimaryKey()
		                                                  ));
		$comments = Comment::buildTree($comments);

		$this->render('commentsTree',
			array(
			     'comments' => $comments,
			));
	}
}