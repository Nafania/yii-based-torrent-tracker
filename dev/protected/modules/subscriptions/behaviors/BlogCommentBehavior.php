<?php
class BlogCommentBehavior extends CActiveRecordBehavior {
	public function afterSave ( $e ) {
		parent::afterSave($e);

		$owner = $this->getOwner();

		if ( !$owner->modelName == 'BlogPost' ) {
			return false;
		}

		$blogPost = BlogPost::model()->findByPk($owner->modelId);
		if ( !$blogPost ) {
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

		$event->save();

		return true;
	}
}