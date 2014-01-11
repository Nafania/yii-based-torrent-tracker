<?php
/**
 * @var Comment[] $comments
 */
?>

<?php
foreach ($comments AS $comment) {

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
				'class' => 'media-object img-polaroid',
				'style' => 'width:32px;height:32px;',
			)),
		$url,
		array('class' => 'pull-left'));
	?>

	<div class="media-body">
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
	        , <?php echo CHtml::link('<abbr title="' . Yii::app()->dateFormatter->formatDateTime($comment->ctime) . '">' . TimeHelper::timeAgoInWords($comment->ctime) . '</abbr>', $comment->getUrl()) ?>

	        <span class="commentOptions">
	        <?php
	        $widget = $this->widget('application.modules.ratings.widgets.CommentsRating',
		        array(
			        'model' => $comment
		        ));
	        $rating = $widget->getRating();
	        ?>
	        </span>

	        </h6>

             <div class="commentText <?php echo 'rating' . ($rating < -10 ? -10 : $rating); ?>"><?php echo TextHelper::parseText($comment->getText(),
		             'comment-' . $comment->getId()); ?></div>
		</div>
	</div>
</div>
<?php
}