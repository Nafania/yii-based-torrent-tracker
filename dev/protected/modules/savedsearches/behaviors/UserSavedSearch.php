<?php
class UserSavedSearch extends CBehavior {
	public function getSavedSearchData ( $modelName ) {
		if ( Yii::app()->getUser()->getIsGuest() ) {
			$data = @unserialize(Yii::app()->request->cookies['Savedsearch' . $modelName]->value);
		}
		else {
			$search = Savedsearch::model()->findByAttributes(array(
			                                                      'modelName' => $modelName,
			                                                      'uId'       => Yii::app()->getUser()->getId(),

			                                                 ));
			$data = ($search ? $search->getData() : array());
		}

		return ($data ? $data : array());
	}
}