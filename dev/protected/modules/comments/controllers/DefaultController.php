<?php

class DefaultController extends components\Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('ajaxOnly + create, loadAnswerBlock, delete'));
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
					array(
						'view'     => $view,
						'parentId' => (int) $comment->parentId,
						'id'       => (int) $comment->getId()
					));
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
		$comment->modelName = $modelName;
		$comment->modelId = $modelId;
		$comment->parentId = $parentId;

		$this->_getAnswerBlock($comment, Yii::app()->createUrl('/comments/default/create'));
	}

	public function actionUpdate () {
		$Comment = Yii::app()->getRequest()->getParam('Comment', array());
		$id = ( !empty($Comment['id']) ? (int) $Comment['id'] : 0 );

		$comment = Comment::model()->findByPk($id);

		if ( !$comment ) {
			throw new CHttpException(404);
		}

		if ( !Yii::app()->getUser()->checkAccess('updateOwnComment',
				array('model' => $comment)) && !Yii::app()->getUser()->checkAccess('updateComment')
		) {
			throw new CHttpException(403);
		}

		$this->performAjaxValidation($comment);

		if ( isset($_POST['Comment']) ) {
			$comment->attributes = $_POST['Comment'];

			if ( $comment->saveNode() ) {
				$view = $this->renderPartial('application.modules.comments.widgets.views._commentView',
					array('comment' => $comment),
					true);

				Ajax::send(Ajax::AJAX_SUCCESS,
					Yii::t('commentsModule.common', 'Комментарий отредактирован успешно'),
					array(
						'view'     => $view,
						'parentId' => (int) $comment->parentId,
					));
			}
			else {
				Ajax::send(Ajax::AJAX_ERROR, Yii::t('commentsModule.common', 'Some errors during save occurred'));
			}
		}

		$this->_getAnswerBlock($comment, Yii::app()->createUrl('/comments/default/update'));
	}

	public function actionDelete () {
		$id = Yii::app()->getRequest()->getPost('id');

		$model = Comment::model()->findByPk($id);

		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( !Yii::app()->getUser()->checkAccess('deleteOwnComment',
				array('model' => $model)) && !Yii::app()->getUser()->checkAccess('deleteComment')
		) {
			throw new CHttpException(403);
		}

		if ( $model->deleteNode() ) {
			\Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('commentsModule.common', 'Комментарий успешно удален'));
		}
		else {
			\Ajax::send(Ajax::AJAX_WARNING,
				Yii::t('commentsModule.common', 'При удалении комментария возникли ошибки'));
		}
	}

	private function _getAnswerBlock ( Comment $comment, $action ) {

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
				'action'    => $action,
				'comment'   => $comment,
				'modelName' => $comment->modelName,
				'modelId'   => $comment->modelId,
				'parentId'  => $comment->parentId
			),
			true,
			true);

		Ajax::send(Ajax::AJAX_SUCCESS,
			'ok',
			array(
				'view'     => $view,
				'parentId' => $comment->parentId
			));
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