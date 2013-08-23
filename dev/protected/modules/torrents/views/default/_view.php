<?php
/**
 * @var $data TorrentGroup
 */
?>
<div class="media">
	<?php
	$img = CHtml::image($data->getImageUrl(100, 0), $data->getTitle(), array('class' => 'media-object img-polaroid', 'style' => 'width:100px'));
	echo CHtml::link($img, $data->getUrl(), array('class' => 'pull-left'));
	?>

	<div class="media-body">
        <h4 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h4>

        <p><?php echo StringHelper::cutStr($data->getDescription()); ?></p>

        <p>
	        <strong><?php echo CHtml::link($data->category->getTitle(), array('/torrents/default/index', 'category' => $data->category->getTitle())) ?></strong><?php
	        if ( $tags = $data->getTags() ) {
		        foreach ( $tags AS $tag ) {
			        echo ', <strong>' . CHtml::link($tag, array('/torrents/default/index', 'tags' => $tag)) . '</strong>';
		        }
	        }
	        foreach ( $data->getSeparateAttributes() AS $id => $attr ) {
		        echo ', ' . CHtml::link($attr,
			        CMap::mergeArray($data->getUrl(), array('#' => 'collapse' . $id)));
	        }
	        ?>
        </p>
    </div>
</div>
<hr />