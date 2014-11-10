<?php
namespace modules\userwarnings\controllers;

use modules\userwarnings\models\UserWarning;
use Yii;
use CMap;

class DefaultController extends \components\Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
		return \CMap::mergeArray(parent::filters(),
			array(
				'ajaxOnly + create',
			));
	}

	public function actionCreate () {
		$uId = Yii::app()->getRequest()->getPost('uId', 0);

		$model = \User::model()->findByPk($uId);

		if ( !$model ) {
			throw new \CHttpException(404);
		}

		if ( !Yii::app()->getUser()->checkAccess('createUserWarning',
				array('model' => $model))
		) {
			throw new \CHttpException(403);
		}

		$warning = new UserWarning();
		$warning->uId = $model->getId();

		$this->performAjaxValidation($warning);

		if ( isset($_POST[$warning->resolveClassName()]) ) {
			$warning->attributes = $_POST[$warning->resolveClassName()];

			if ( $warning->save() ) {
				\Ajax::send(\Ajax::AJAX_SUCCESS,
					Yii::t('userwarningsModule.common', 'Предупреждение успешно сохранено'));
			}
			else {
				\Ajax::send(\Ajax::AJAX_WARNING,
					Yii::t('userwarningsModule.common', 'При сохранениии предупреждения возникли ошибки'));
			}
		}

		Yii::app()->getClientScript()->scriptMap['jquery.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.min.js'] = false;
		Yii::app()->getClientScript()->scriptMap['jquery.yiiactiveform.js'] = false;

		$view = $this->renderPartial('create',
			array(
				'warning' => $warning,
			),
			true,
			true);

		\Ajax::send(\Ajax::AJAX_SUCCESS, 'ok', array('view' => $view));
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation ( $model ) {
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'warning-form' ) {
			echo \CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
