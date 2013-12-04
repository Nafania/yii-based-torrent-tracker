<?php

class DefaultController extends components\Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + create, loadAnswerBlock'));
	}

	public function actionCreate () {
		$comment = new Comment();

		$this->performAjaxValidation($comment);

		if ( isset($_POST['Comment']) ) {
			$comment->attributes = $_POST['Comment'];

			if ( $comment->saveNode() ) {
				$view = $this->renderPartial('application.modules.comments.widgets.views._commentView',
					array('comment' => $comment),
					true);

				Ajax::send(Ajax::AJAX_SUCCESS,
					Yii::t('commentsModule.common', 'Comment added successfully'),
					array('view' => $view, 'parentId' => (int) $comment->parentId, 'id' => (int) $comment->getId()));
			}
			else {
				Ajax::send(Ajax::AJAX_ERROR, Yii::t('commentsModule.common', 'Some errors during save occurred'));
			}
		}
	}

	public function actionLoadAnswerBlock () {
		$modelName = Yii::app()->getRequest()->getParam('modelName', '');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$parentId = Yii::app()->getRequest()->getParam('parentId', 0);

		$comment = new Comment();

		/**
		 * disable all scripts
		 */
		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.min.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.yiiactiveform.js'] = false;
		Yii::app()->getClientScript()->scriptMap['redactor.min.js'] = false;
		Yii::app()->getClientScript()->scriptMap['redactor.js'] = false;
		Yii::app()->getClientScript()->scriptMap['ru.js'] = false;
		//var_dump(Yii::app()->getClientScript()->scriptMap);
		$view = $this->renderPartial('application.modules.comments.widgets.views.answer',
			array(
			     'comment' => $comment,
			     'modelName' => $modelName,
			     'modelId'   => $modelId,
			     'parentId'  => $parentId
			),
			true,
			true);

		Ajax::send(Ajax::AJAX_SUCCESS, 'ok', array('view' => $view, 'parentId' => $parentId));
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param $model array the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}