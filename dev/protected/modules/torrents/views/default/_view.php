<?php
/**
 * @var $data TorrentGroup
 */
?>
<div class="media">
	<?php
	$img = CHtml::image($data->getImageUrl(80, 110), $data->getTitle(), array('class' => 'media-object img-polaroid'));
	echo CHtml::link($img, $data->getUrl(), array('class' => 'pull-left'));
	?>

	<div class="media-body">
        <h4 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h4>

        <p><?php echo $data->getDescription(); ?></p>

        <p>
	        <strong><?php echo CHtml::link($data->category->getTitle()) ?></strong>
	        <?php
	        if ( $tags = $data->getTags() ) {
		        foreach ( $tags AS $tag ) {
			        echo ', <strong>' . CHtml::link($tag) . '</strong>';
		        }
	        }
	        foreach ( $data->getSeparateAttributes() AS $attr ) {
		        echo ', ' . CHtml::link($attr,
			        CMap::mergeArray($data->getUrl(), array('#' => 'collapse' . md5($attr))));
	        }
	        ?>
        </p>
    </div>
</div>
<hr />