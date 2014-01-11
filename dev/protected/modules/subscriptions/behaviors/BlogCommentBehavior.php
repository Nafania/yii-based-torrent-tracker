<?php
class BlogCommentBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		$owner = $this->getOwner();

		if ( $owner->modelName != 'modules_blogs_models_BlogPost' ) {
			return false;
		}

		$blogPost = modules\blogs\models\BlogPost::model()->findByPk($owner->modelId);
		if ( !$blogPost || $blogPost->ownerId == Yii::app()->getUser()->getId() ) {
			return false;
		}

		$url = $owner->getUrl();
		$icon = 'comment';

		$event = new Event();
		$event->text = Yii::t('subscriptionsModule.common',
			'Добавлен новый комментарий к вашей записи "{title}"',
			array(
			     '{title}' => $blogPost->getTitle()
			));
		$event->title = Yii::t('subscriptionsModule.common', 'Новый комменатрий к вашей записи');
		$event->url = $url;
		$event->icon = $icon;
		$event->uId = $blogPost->ownerId;
		$event->uniqueType = $icon . $owner->modelName . $blogPost->getPrimaryKey();

		return $event->save();
	}
}