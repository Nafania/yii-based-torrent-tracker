<?php

class DefaultController extends Controller {

	public function actionUpload () {
		$modelName = Yii::app()->getRequest()->getParam('modelName');
		$modelId = Yii::app()->getRequest()->getParam('modelId', 0);
		$description = Yii::app()->getRequest()->getParam('description', '');
		if ( !$modelName ) {
			throw new CHttpException(403);
		}

		$file = CUploadedFile::getInstanceByName('file');

		$model = new File();
		$model->file = $file;
		$model->modelName = $modelName;
		$model->modelId = $modelId;
		$model->description = $description;
		if ( $model->save() ) {
			$array = array(
				'filelink' => $model->getFileUrl(),
			);
			echo CJSON::encode($array);
			Yii::app()->end();
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, '500');
		}
	}
}