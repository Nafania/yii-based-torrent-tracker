<?php
/**
 * @var $data BlogPost
 */
?>

<div class="media">

    <div class="media-body">
	    <h2 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h2>
	    <?php echo StringHelper::cutStr($data->getText(), 500, '<p>' . CHtml::link(Yii::t('blogsModule.common', 'Читать далее...'), array('/blogs/post/view', 'id' => $data->getId())) . '</p>'); ?>
	    <p>
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
				    $tagsStr .= ( $tagsStr ? ', ' : '' ) . '<strong>' . CHtml::link($tag,
					    array(
					         '/blogs/default/view',
					         'id' => $data->blog->getId(),
					         'tags' => $tag
					    )) . '</strong>';
			    }
			    echo ' | ' . $tagsStr;
		    }
		    ?>
     </p>
	</div>
</div>
<hr />