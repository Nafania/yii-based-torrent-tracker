<?php
/**
 * @var $posts modules\blogs\models\BlogPost[]
 */
?>

<?php foreach ( $posts AS $key => $post ) { ?>
	<div class="accordion-group">
            <div class="accordion-heading">
	            <?php echo CHtml::link($post->getTitle(), $post->getUrl()); ?>, <abbr title="<?php echo $post->getCtime('d.m.Y H:i'); ?>"><?php echo TimeHelper::timeAgoInWords($post->getCtime()) ?></abbr>,
	            <?php echo CHtml::link($post->user->getName(), $post->user->getUrl()) ?>
                <?php
          		    if ( $commentsCount = $post->commentsCount->count ) {
          			    echo ', ' . CHtml::link('<i class="icon-comment"></i> ' . $commentsCount, CMap::mergeArray($post->getUrl(), ['#' => 'comments']));
          		    }
                ?>
            </div>
            <div>
                <div class="accordion-inner">
	                <?php echo StringHelper::cutStr($post->getText(),
		                200,
		                CHtml::link(Yii::t('blogsModule.common', 'читать далее >>>'), $post->getUrl())) ?>
                </div>
            </div>
        </div>


<?php } ?>