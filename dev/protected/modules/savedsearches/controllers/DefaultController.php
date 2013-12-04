<?php

class DefaultController extends components\Controller {
	/**
	 * @return array action filters
	 */
	public function filters () {
		return CMap::mergeArray(parent::filters(),
			array(
			     'ajaxOnly + create',
			));
	}

	public function actionCreate ( $modelName ) {
		if ( !class_exists($modelName) ) {
			throw new CHttpException(404);
		}

		$data = array();
		foreach ( $_POST AS $param => $val ) {
			if ( $param == Yii::app()->getRequest()->csrfTokenName || !$val ) {
				continue;
			}

			$data[$param] = $val;
		}

		if ( Yii::app()->getUser()->getIsGuest() ) {
			if ( $data ) {
				Yii::app()->request->cookies['Savedsearch' . $modelName] = new CHttpCookie('Savedsearch' . $modelName, serialize($data), array('expire' => time() + 30 * 24 * 60 * 60));
				Ajax::send(Ajax::AJAX_SUCCESS,
					Yii::t('savedsearchesModule.common', 'Настройки поиска успешно сохранены'));
			}
			else {
				unset(Yii::app()->request->cookies['Savedsearch' . $modelName]);
				Ajax::send(Ajax::AJAX_SUCCESS,
					Yii::t('savedsearchesModule.common', 'Настройки поиска успешно удалены'));
			}
		}
		else {
			$models = Savedsearch::model()->findAllByAttributes(array(
			                                                 'modelName' => $modelName,
			                                                 'uId'       => Yii::app()->getUser()->getId(),
			                                            ));
			foreach ( $models AS $model ) {
				$model->delete();
			}


			if ( $data ) {
				$search = new Savedsearch();
				$search->uId = Yii::app()->getUser()->getId();
				$search->modelName = $modelName;
				$search->data = $data;
				if ( $search->save() ) {
					Ajax::send(Ajax::AJAX_SUCCESS,
						Yii::t('savedsearchesModule.common', 'Настройки поиска успешно сохранены'));
				}
				else {
					Ajax::send(Ajax::AJAX_ERROR,
						Yii::t('savedsearchesModule.common',
							'При сохранении настроек поиска возникла ошибка, попробуйте сохранить настройки позже.'));
				}
			}
			else {
				Ajax::send(Ajax::AJAX_SUCCESS,
					Yii::t('savedsearchesModule.common', 'Настройки поиска успешно удалены'));
			}
		}
	}
}