<?php

class CommentsBackendController extends YAdminController {

	public function filters () {
		return parent::filters();
	}

	public function init () {
		parent::init();
	}


	public function actionIndex () {
		$this->breadcrumbs[] = Yii::t('commentsModule.common', 'Управление комментариями');
		$this->pageTitle = Yii::t('commentsModule.common', 'Управление комментариями');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$model = new Comment();
		$model->setScenario('adminSearch');
		$model->unsetAttributes();

		if ( isset($_GET[get_class($model)]) ) {
			$model->setAttributes($_GET[get_class($model)]);
		}

		$criteria = new CDbCriteria();
		$criteria->order = 'ctime DESC';

		$dataProvider = $model->search();
		$dataProvider->getCriteria()->mergeWith($criteria);

		Ajax::renderAjax('index',
			array(
			     'model'        => $model,
			     'dataProvider' => $dataProvider,
			),
			false,
			false,
			true);
	}


	public function actionDelete ( $id ) {
		$model = Comment::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( $model->deleteNode() ) {
			Ajax::send(Ajax::AJAX_SUCCESS, 'ok');
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, 'error');
		}
	}
}