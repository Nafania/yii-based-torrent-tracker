<?php
/**
 * @var $data modules\blogs\models\BlogPost
 */
?>

<div class="media">

    <div class="media-body">
	    <h2 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h2>
	    <?php echo StringHelper::cutStr($data->getText(),
		    500,
		    '<p class="clearfix">' . CHtml::link(Yii::t('blogsModule.common', 'Читать далее...'),
			    $data->getUrl()) . '</p>'); ?>
	    <p>
		    <?php
		    if ( $commentsCount = $data->commentsCount->count ) {
			    echo CHtml::link('<i class="icon-comment"></i> ' . $commentsCount,
					    CMap::mergeArray($data->getUrl(), array('#' => 'comments'))) . ', ';
		    }
		    ?>
		    <abbr title="<?php echo Yii::t('blogsModule.common',
			    'Добавлено: {date}',
			    array(
			         '{date}' => $data->getCtime('d.m.Y H:i')
			    )) ?>"><?php echo TimeHelper::timeAgoInWords($data->ctime); ?></abbr>
		    |
		    <?php echo CHtml::link($data->user->getName(), $data->user->getUrl()); ?>
		    <?php
		    if ( $tags = $data->getTags() ) {
			    $tagsStr = '';
			    foreach ( $tags AS $tag ) {
				    $tagsStr .= ($tagsStr ? ', ' : '') . '<strong>' . CHtml::link($tag,
						    CMap::mergeArray($data->blog->getUrl(), array('tags' => $tag))) . '</strong>';
			    }
			    echo ' | ' . $tagsStr;
		    }
		    ?>
     </p>
	</div>
</div>