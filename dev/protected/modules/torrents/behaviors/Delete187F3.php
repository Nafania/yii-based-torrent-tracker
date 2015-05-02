<?php

class Delete187F3 extends CActiveRecordBehavior {
	public function checkIsBlocked () {
		$model = \modules\torrents\models\Delete187F3::model()->findByPk($this->getOwner()->getPrimaryKey());

		if ( $model ) {
			$countryCode = geoip_country_code_by_name(Yii::app()->getRequest()->getUserHostAddress());
			if ( $countryCode == $model->country_code ) {
				throw new \CHttpException(403, Yii::t('torrentsModule.common', 'Данный контент недоступен пользователям из страны {countryCode}. {reason}', array(
					'{reason}' => ( $model->reason ? PHP_EOL . 'Причина: ' . $model->reason : '' ),
                    '{countryCode}' => $model->country_code,
				)));
			}
		}

		return $this->getOwner();
	}
}