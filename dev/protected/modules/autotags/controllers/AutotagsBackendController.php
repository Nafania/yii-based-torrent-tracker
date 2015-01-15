<?php

class AutotagsBackendController extends YAdminController {

	public function actionIndex () {
		$this->breadcrumbs[] = Yii::t('autotagsModule.common', 'Управление авто тегами');
		$this->pageTitle = Yii::t('autotagsModule.common', 'Управление авто тегами');

		Yii::import('yiiadmin.extensions.yiiext.zii.widgets.grid.*');

		$data = Yii::app()->getDb()->createCommand('SELECT id_auto_tag, t.name AS tag_name, c.name AS cat_name FROM auto_tag at, tags t, categories c WHERE at.fk_tag = t.id AND at.fk_category = c.id')->queryAll();

		$dataProvider = new CArrayDataProvider($data, [
			'keyField' => 'id_auto_tag',
		]);

		Ajax::renderAjax('index',
			[
			     'dataProvider'    => $dataProvider,
			],
			false,
			false,
			true);
	}

	public function actionCreate () {
		$this->breadcrumbs[] = CHtml::link(Yii::t('staticpagesModule.common', 'Управление статичными страницами'), $this->createUrl('/staticpages/staticpagesBackend/index'));
		$this->breadcrumbs[] = Yii::t('staticpagesModule.common', 'Создание страницы');
		$this->pageTitle = Yii::t('staticpagesModule.common', 'Создание страницы');

		$model = new AutoTag();

		if ( isset($_POST[get_class($model)]) ) {
			$model->attributes = $_POST[get_class($model)];

			if ( $model->save() ) {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('autotagsModule.common', 'Страница успешно создана'));
				$this->redirect($this->createUrl('/autotags/autotagsBackend/index'));
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage', Yii::t('autotagsModule.common', 'При сохранении страницы возникли ошибки'));
			}
		}

		Ajax::renderAjax('create',
			[
			     'model'    => $model,
			],
			false,
			false,
			true);
	}

	public function actionDelete ( $id ) {
		$model = AutoTag::model()->findByPk($id);
		if ( !$model ) {
			throw new CHttpException(404);
		}

		if ( $model->delete() ) {
			Ajax::send(Ajax::AJAX_SUCCESS, 'ok');
		}
		else {
			Ajax::send(Ajax::AJAX_ERROR, 'error');
		}
	}
}