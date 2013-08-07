<?php
class AnswerWidget extends CWidget {
	public $model;
	public $modelId;
	public $modelName;
	public $parentId = 0;
	public $torrents;

	public function init () {
		parent::init();

		Yii::import('application.modules.comments.models.*');

		if ( $this->model ) {
			$this->modelId = $this->model->getPrimaryKey();
			$this->modelName = get_class($this->model);
		}

		if ( !$this->modelName || !$this->modelId ) {
			throw new CException('Not enough data');
		}
	}

	public function run () {
		$comment = new Comment();

		$this->render('answer', array(
		                                   'comment' => $comment,
		                                   'modelId' => $this->modelId,
		                                   'modelName' => $this->modelName,
		                                   'parentId' => $this->parentId,
		                                   'torrents' => $this->torrents,
		                              ));
	}
}