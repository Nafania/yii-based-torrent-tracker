<?php

class DefaultController extends components\Controller {
	public function filters () {
		return CMap::mergeArray(parent::filters(), array('postOnly + upload, delete'));
	}

	public function actionUpload () {
		$modelName = Yii::app()->getRequest()->getParam('modelName');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$description = Yii::app()->getRequest()->getParam('description', '');
		if ( !$modelName ) {
			throw new CHttpException(403);
		}

		$files = CUploadedFile::getInstancesByName('file');
		if ( !$files ) {
			$files = array(CUploadedFile::getInstanceByName('file'));
		}

		foreach ( $files AS $file ) {
			$model = new File();
			$model->file = $file;
			$model->modelName = $modelName;
			$model->modelId = $modelId;
			$model->description = $description;
			if ( $model->save() ) {
				$array = array(
					'filelink' => $model->getFileUrl(),
					'filename' => $model->getFileUrl(),
					'success'  => true,
					'id'       => $model->getId(),
				);
				echo CJSON::encode($array);
				Yii::app()->end();
			}
			else {
				Ajax::send(Ajax::AJAX_ERROR,
					'500',
					array(
						'errors' => $model->getErrors()
					));
			}
		}
	}

	public function actionDelete () {
		$id = Yii::app()->getRequest()->getParam('id');

		$file = File::model()->findByPk($id);
		if ( !$file ) {
			throw new CHttpException(404, Yii::t('filesModule.common', 'Указанный файл не найден'));
		}

		if ( !Yii::app()->user->checkAccess('deleteOwnFile',
				array('ownerId' => $file->ownerId)) && !Yii::app()->user->checkAccess('deleteFile')
		) {
			throw new CHttpException(403, Yii::t('filesModule.common', 'Вы не можете удалить не свой файл'));
		}

		$file->delete();
	}

	public function actionIndex ( $modelName, $id ) {
		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.min.js'] = false;

		$files = File::model()->findAllByAttributes(array(
			'modelName' => $modelName,
			'modelId'   => $id,
		));

		if ( !$files ) {
			throw new CHttpException(404);
		}

		Ajax::renderAjax('index',
			array(
				'files' => $files
			), false, true, true);
	}
}