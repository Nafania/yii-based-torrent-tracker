<?php
class DeleteDraftBehavior extends CActiveRecordBehavior {
	public function afterSave($e) {
		parent::afterSave($e);

		Yii::import('application.modules.drafts.models.*');

		if ( $formId = Yii::app()->getUser()->getState('draft' . get_class($this->getOwner())) ) {
			$draft = Draft::model()->findByPk($formId);
			if ( $draft ) {
				$draft->deleted = Draft::DELETED;
				$draft->save(false);
			}
		}

		return true;
	}
}