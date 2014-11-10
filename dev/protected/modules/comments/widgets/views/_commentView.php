<?php
/**
 * @var $comment Comment
 */
?>
<?php
$tid = $comment->getTorrentId();
?>

<div class="media commentContainer" data-comments-for="<?php echo $tid ?>" id="comment-<?php echo $comment->getId(); ?>" data-id="<?php echo $comment->getId(); ?>">

	<?php
	if ( $comment->user ) {
		$img = $comment->user->profile->getImageUrl(50, 50);
		$alt = $comment->user->getName();
		$url = $comment->user->getUrl();
	}
	else {
		$img = '/images/no_photo.png';
		$alt = '';
		$url = '';
	}
	echo CHtml::link(CHtml::image($img,
			$alt,
			array(
				'class' => 'media-object img-polaroid',
				'style' => 'width:50px;height:50px;',
			)),
		$url,
		array('class' => 'pull-left'));
	?>

	<div class="media-body">
		<a name="comment-<?php echo $comment->getId(); ?>"></a>
		<div class="comment">
        <h6 class="media-heading">
	        <?php
	        if ( $comment->user ) {
		        echo CHtml::link($comment->user->getName(), $comment->user->getUrl());
		        echo '<span class="userRating ' . $comment->user->getRatingClass() . '">' . $comment->user->getRating() . '</span>';
	        }
	        else {
		        echo '<i>' . Yii::t('userModule.common', 'Аккаунт удален') . '</i>';
	        }
	        ?>
	        , <abbr title="<?php echo Yii::app()->dateFormatter->formatDateTime($comment->ctime); ?>"><?php echo TimeHelper::timeAgoInWords($comment->ctime); ?></abbr>
	        <?php
	        if ( $comment->getIsNew() ) {
		        echo ' <span class="labelComment label label-info">' . Yii::t('commentsModule.common',
				        'новый') . '</span>';
	        }
	        ?>
	        <?php if ( $tid ) {
		        echo '<small> ' . Yii::t('torrentsModule.common',
				        'для {title}',
				        array('{title}' => $comment->torrentGroup->getSeparateAttribute($tid))) . '</small>';
	        }?>
	        <span class="commentOptions">
	        <?php
	        $widget = $this->widget('application.modules.ratings.widgets.CommentsRating',
		        array(
			        'model' => $comment
		        ));
	        $rating = $widget->getRating();
	        ?>
		        <a href="<?php echo Yii::app()->createUrl('/reports/default/create/'); ?>" data-action="report" data-model="<?php echo $comment->resolveClassName(); ?>" data-id="<?php echo $comment->getId(); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo Yii::t('reportsModule.common',
			        'Пожаловаться на комментарий'); ?>"><i class="icon-warning-sign"></i></a>

		        <?php if ( Yii::app()->getUser()->checkAccess('updateOwnComment',
				        array('model' => $comment)) || Yii::app()->getUser()->checkAccess('updateComment')
		        ) { ?>
			        <a href="<?php echo Yii::app()->createUrl('/comments/default/update'); ?>" data-action="updateComment" data-id="<?php echo $comment->getId(); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo Yii::t('commentsModule.common',
				        'Редактировать комментарий'); ?>"><i class="icon-edit"></i></a>
		        <?php } ?>

		        <?php if ( Yii::app()->getUser()->checkAccess('deleteOwnComment',
				        array('model' => $comment)) || Yii::app()->getUser()->checkAccess('deleteComment')
		        ) { ?>
			        <a href="<?php echo Yii::app()->createUrl('/comments/default/delete'); ?>" data-action="deleteComment" data-id="<?php echo $comment->getId(); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo Yii::t('commentsModule.common',
				        'Удалить комментарий'); ?>"><i class="icon-trash"></i></a>
		        <?php } ?>
	        </span>

	        </h6>

             <div class="commentText commentTextGradient <?php echo 'rating' . ($rating < -10 ? -10 : $rating); ?>">
                 <?= TextHelper::parseText($comment->getText(), 'comment-' . $comment->getId()); ?>
             </div>

			<?php if ( Yii::app()->getUser()->checkAccess('comments.default.loadAnswerBlock') && $comment->status == $comment::APPROVED ) { ?>
				<span><?php echo CHtml::link(Yii::t('commentsModule.common', 'Ответить'),
						'#',
						array('class' => 'commentReply')); ?></span>
			<?php } ?>
		</div>
		<?php if ( count($comment->childs) > 0 ) {
			$this->render('_commentsTree', array('comments' => $comment->childs));
		}?>
	</div>
</div>