<?php
class AutoCreateBlogForGroup extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		/**
		 * @var $owner Group
		 */
		$owner = $this->getOwner();

		if ( !$owner->getIsNewRecord() ) {
			return;
		}

		$blog = new modules\blogs\models\Blog();
		$blog->title = $blog->description = Yii::t('blogsModule.common',
			'Блог группы "{groupName}"',
			array('{groupName}' => $owner->getTitle()));
		$blog->ownerId = Yii::app()->getUser()->getId();
		$blog->groupId = $owner->getId();
		$blog->save();
	}
}