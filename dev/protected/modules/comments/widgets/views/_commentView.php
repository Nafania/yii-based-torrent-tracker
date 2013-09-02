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
		$img = $comment->user->profile->getImageUrl(32, 32);
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
		     'class'  => 'media-object',
		     'width'  => '32',
		     'height' => '32'
		)),
		$url,
		array('class' => 'pull-left'));
	?>

	<div class="media-body comment">
        <h6 class="media-heading">
	        <?php
	        if ( $comment->user ) {
		        echo CHtml::link($comment->user->getName(), $comment->user->getUrl());
	        }
	        else {
		        echo '<i>' . Yii::t('userModule.common', 'Account deleted') . '</i>';
	        }
	        ?>
	        , <abbr title="<?php echo Yii::app()->dateFormatter->formatDateTime($comment->ctime); ?>"><?php echo TimeHelper::timeAgoInWords($comment->ctime);?></abbr>
	        <?php if ( $tid ) {
		        echo '<small> ' . Yii::t('torrentsModule.common',
			        'for') . ' ' . $comment->torrentGroup->getSeparateAttribute($tid) . '</small>';
	        }?>
	        <?php
	        $widget = $this->widget('application.modules.ratings.widgets.CommentsRating',
		        array(
		             'model' => $comment
		        ));
	        $rating = $widget->getRating();
	        ?>
	        <a href="<?php echo Yii::app()->createUrl('/reports/default/create/',
		        array(
		             'modelName' => get_class($comment),
		             'modelId'   => $comment->getId()
		        )); ?>" data-action="report" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('reportsModule.common',
		        'Пожаловаться на комментарий'); ?>"><i class="icon-warning-sign"></i></a>
	        </h6>

        <div class="commentText <?php echo 'rating' . ($rating < -10 ? -10 : $rating); ?>"><?php echo TextHelper::parseText($comment->getText());?></div>
		<?php if ( Yii::app()->getUser()->checkAccess('comments.default.loadAnswerBlock') ) { ?>
			<span><?php echo CHtml::link(Yii::t('commentsModule.common', 'Reply'),
					'#',
					array('class' => 'commentReply')); ?></span>
		<?php } ?>

		<?php if ( count($comment->childs) > 0 ) {
			$this->render('_commentsTree', array('comments' => $comment->childs));
		}?>
	</div>
</div>