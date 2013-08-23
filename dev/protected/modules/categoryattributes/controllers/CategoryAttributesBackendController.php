<?php

class CategoryAttributesBackendController extends YAdminController {

	public function filters () {
		return CMap::mergeArray(array(
		                             'ajaxOnly + delete',
		                        ),
			parent::filters());
	}

	public function init () {
		parent::init();

		$this->breadcrumbs[] = CHtml::link(Yii::t('CategoryAttributesModule', 'Управление атрибутами'), Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/'));
	}

	public function actionIndex () {
		//$this->breadcrumbs[] = Yii::t('CategoryAttributesModule', 'Управление аттрибутами');
		$this->pageTitle = Yii::t('CategoryAttributesModule', 'Управление аттрибутами');

		$model = new Attribute('adminSearch');

		$model->unsetAttributes();

		if ( isset($_GET[get_class($model)]) ) {
			$model->setAttributes($_GET[get_class($model)]);
		}

		Ajax::renderAjax('index',
			array(
			     'model'     => $model,
			));
	}

	public function actionCreate () {
		Yii::import('application.modules.category.models.*');

		$this->breadcrumbs[] = Yii::t('CategoryAttributesModule', 'Создание аттрибута');
		$this->pageTitle = Yii::t('CategoryAttributesModule', 'Создание аттрибута');

		$model = new Attribute('adminCreate');
		$chars = array(new CategoryAttrChars('adminCreate'));
		$counter = 0;

		$this->performAjaxValidation(CMap::mergeArray(array($model), $chars));

		if ( isset($_POST['Attribute']) ) {
			$model->attributes = $_POST['Attribute'];

			$valid = $model->validate();
			if ( $model->isCharacteristicsNeeded() ) {
				$CategoryAttrChars = Yii::app()->getRequest()->getPost('CategoryAttrChars', array());
				$chars = array();
				foreach ( $CategoryAttrChars AS $i => $data ) {
					$chars[$i] = new CategoryAttrChars('adminCreate');
					$chars[$i]->attributes = $data;
					$chars[$i]->order = $i;
					$valid = $valid && $chars[$i]->validate();
				}
				$counter = sizeof($chars) - 1;
			}

			if ( $valid ) {
				try {
					$model->save(false);
					if ( $model->isCharacteristicsNeeded() ) {
						foreach ( $chars AS $char ) {
							$char->attrId = $model->id;
							$char->save(false);
						}
					}

					Yii::app()->getUser()->setFlash('flashMessage',
						Yii::t('CategoryAttributesModule', 'Атрибуты сохранены успешно'));

					$this->redirect(Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/'));
				} catch ( CException $e ) {
					Yii::app()->getUser()->setFlash('flashMessage',
						Yii::t('CategoryAttributesModule', 'Возникли ошибки при сохранении атрибутов'));
				}
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage',
					Yii::t('CategoryAttributesModule', 'Возникли ошибки при сохранении атрибутов'));
			}

			//if ( Ajax::saveModel($model) ) {
			//	$this->redirect(Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/'));
			//}
		}

		Ajax::renderAjax('create',
			array(
			     'model'   => $model,
			     'chars'   => $chars,
			     'counter' => $counter
			));
	}


	public function actionUpdate ( $id ) {
		Yii::import('application.modules.category.models.*');

		$this->breadcrumbs[] = Yii::t('CategoryAttributesModule', 'Редактирование аттрибута');
		$this->pageTitle = Yii::t('CategoryAttributesModule', 'Редактирование аттрибута');

		$model = Attribute::model()->findByPk($id);
		if ( $model == null ) {
			throw new CHttpException(404);
		}
		$model->setScenario('adminUpdate');
		$chars = ( $model->chars ? $model->chars : array(new CategoryAttrChars('adminCreate')) );
		$counter = sizeof($chars) - 1;
		$validators = CValidator::$builtInValidators;

		$this->performAjaxValidation(CMap::mergeArray(array($model), $chars));

		if ( isset($_POST['Attribute']) ) {
			$model->attributes = $_POST['Attribute'];

			$valid = $model->validate();
			if ( $model->isCharacteristicsNeeded() ) {
				$CategoryAttrChars = Yii::app()->getRequest()->getPost('CategoryAttrChars', array());
				//$chars = array();
				foreach ( $CategoryAttrChars AS $i => $data ) {
					if ( empty($chars[$i]) ) {
						$chars[$i] = new CategoryAttrChars('adminUpdate');
					}
					$chars[$i]->attributes = $data;
					$chars[$i]->order = $i;
					$valid = $valid && $chars[$i]->validate();
				}
				$counter = sizeof($chars) - 1;
			}

			if ( $valid ) {
				try {
					$model->save(false);
					if ( $model->isCharacteristicsNeeded() ) {
						foreach ( $chars AS $char ) {
							$char->attrId = $model->id;
							$char->save(false);
						}
					}

					Yii::app()->getUser()->setFlash('flashMessage',
						Yii::t('CategoryAttributesModule', 'Атрибуты сохранены успешно'));

					//	$this->redirect(Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/'));
				} catch ( CException $e ) {
					Yii::app()->getUser()->setFlash('flashMessage',
						Yii::t('CategoryAttributesModule', 'Возникли ошибки при сохранении атрибутов'));
				}
			}
			else {
				Yii::app()->getUser()->setFlash('flashMessage',
					Yii::t('CategoryAttributesModule', 'Возникли ошибки при сохранении атрибутов'));
			}

			//if ( Ajax::saveModel($model) ) {
			//	$this->redirect(Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/'));
			//}
		}

		Ajax::renderAjax('update',
			array(
			     'model'   => $model,
			     'chars'   => $chars,
			     'counter' => $counter,
			     'validators' => $validators,
			     'action'  => Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/update',
				     array('id' => $id))
			));
	}

	public function actionDelete ( $id ) {
		$this->breadcrumbs[] = Yii::t('CategoryAttributesModule', 'Удаление аттрибута');
		$this->pageTitle = Yii::t('CategoryAttributesModule', 'Удаление аттрибута');

		$model = Attribute::model()->findByPk($id);
		if ( $model == null ) {
			throw new CHttpException(404);
		}
		try {
			$model->setScenario('adminDelete');
			$model->delete();
			Ajax::send(Ajax::AJAX_SUCCESS, Yii::t('CategoryAttributesModule', 'Атрибуты удалены успешно'));
		} catch ( CException $e ) {
			Ajax::send(Ajax::AJAX_ERROR, Yii::t('CategoryAttributesModule', 'Возникли ошибки при удалении атрибутов'));
		}
	}

	public function performAjaxValidation ( $models ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'category-attributes-form' ) {
			echo CActiveForm::validate($models);
			Yii::app()->end();
		}
	}
}