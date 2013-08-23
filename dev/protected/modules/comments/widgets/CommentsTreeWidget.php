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
		$criteria = new CDbCriteria();
		$criteria->condition = 't.modelName = :modelName AND t.modelId = :modelId';
		$criteria->params = array(
			'modelName' => get_class($this->model),
			'modelId' => $this->model->getPrimaryKey(),
		);
		//TODO: when cache enabled comments not shown with anger loading
		//$comments = Comment::model()->with(array('user', 'user.profile', 'rating', 'torrent', 'torrent.torrentGroup'))->findAll($criteria);
		$comments = Comment::model()->findAll($criteria);

		$comments = Comment::buildTree($comments);

		$this->render('commentsTree', array(
		                                   'comments' => $comments,
		                              ));
	}
}