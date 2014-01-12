<?php
/**
 * @var \modules\torrents\models\Torrent[] $torrents
 */
?>

<?php
foreach ( $torrents AS $torrent ) {
	?>

	<div class="media">

	<?php
	$img = $torrent->torrentGroup->category->getImageUrl(40, 40);
	$alt = $torrent->torrentGroup->category->getTitle();
	$url = $torrent->torrentGroup->category->getUrl();

	echo CHtml::link(CHtml::image($img,
			$alt,
			array(
				'class' => 'media-object',
				'style' => 'width:40px;height:40px;',
			)),
		$url,
		array('class' => 'pull-left'));
	?>

		<div class="media-body">
	        <?php
	        echo '<strong>' . CHtml::link($torrent->getTitle(),
			        $torrent->getUrl()) . '</strong><br>' . CHtml::link($torrent->torrentGroup->category->getTitle(),
			        array(
				        '',
				        'category[]' => $torrent->torrentGroup->category->getTitle()
			        ));
	        if ( $tags = $torrent->getTags() ) {
		        foreach ( $tags AS $tag ) {
			        echo ', ' . CHtml::link($tag,
					        array(
						        '/torrents/default/index',
						        'tags' => $tag
					        ));
		        }
	        }
	        ?>
	</div>
</div>
<?php
}