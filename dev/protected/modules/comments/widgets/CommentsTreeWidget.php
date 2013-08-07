<?php
class CommentsTreeWidget extends CWidget {
	public $model;

	public function init () {
		parent::init();

		Yii::import('application.modules.comments.models.*');

		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile(Yii::app()->getModule('comments')->getAssetsUrl() . '/comments.js');
		$cs->registerScript('commentModule', 'var commentsUrl = ' . CJavaScript::encode(Yii::app()->createUrl('/comments/default/loadAnswerBlock')) . ';', CClientScript::POS_HEAD);
	}

	public function run () {
		//TODO: when cache enabled comments not shown with anger loading
		$comments = Comment::model()->with(array('user', 'user.profile', 'rating', 'torrent', 'torrent.torrentGroup'))->findAllByAttributes(array(
		                                                       'modelName' => get_class($this->model),
		                                                       'modelId' => $this->model->getPrimaryKey()
		                                                  ));
		/*$comments = Comment::model()->with()->findAllByAttributes(array(
				                                                       'modelName' => get_class($this->model),
				                                                       'modelId' => $this->model->getPrimaryKey()
				                                                  ));*/

		$comments = Comment::buildTree($comments);

		$this->render('commentsTree', array(
		                                   'comments' => $comments,
		                              ));
	}
}