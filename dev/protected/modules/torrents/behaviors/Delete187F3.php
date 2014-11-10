<?php

class Delete187F3 extends CActiveRecordBehavior {
	public function deleteForRussianOrError () {
		$model = \modules\torrents\models\Delete187F3::model()->findByPk($this->getOwner()->getPrimaryKey());

		if ( $model ) {
			$countryCode = geoip_country_code_by_name(Yii::app()->getRequest()->getUserHostAddress());
			if ( $countryCode == 'RU' ) {
				throw new \CHttpException(403, Yii::t('torrentsModule.common', 'Данный контент недоступен пользователям из Российской Федерации в связи с Федеральным законом от 2 июля 2013 года № 187-ФЗ. {reason}', array(
					'{reason}' => ( $model->reason ? PHP_EOL . 'Причина: ' . $model->reason : '' ),
				)));
			}
		}

		return $this->getOwner();
	}
}