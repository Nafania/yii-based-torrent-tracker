<?php
/**
 * @var $data modules\torrents\models\TorrentGroup
 */
?>
<div class="media">
<?php
$img = CHtml::image($data->getImageUrl(100, 0),
	$data->getTitle(),
	array(
		'class' => 'media-object img-polaroid',
		'style' => 'width:100px'
	));
echo CHtml::link($img, $data->getUrl(), array('class' => 'pull-left'));
?>

	<div class="media-body">
	<h4 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h4>

	<p><?php echo StringHelper::cutStr($data->getDescription()); ?></p>

	<p>
<?php
if ( $commentsCount = $data->commentsCount->count ) {
	echo CHtml::link('<i class="icon-comment"></i> ' . $commentsCount,
			CMap::mergeArray($data->getUrl(), array('#' => 'comments'))) . ', ';
}
?>
		<strong><?php echo CHtml::link($data->category->getTitle(),
				array(
					'/torrents/default/index',
					'category[]' => $data->category->getTitle()
				)) ?></strong><?php
		if ( $tags = $data->getTags() ) {
			foreach ( $tags AS $tag ) {
				echo ', <strong>' . CHtml::link($tag,
						array(
							'/torrents/default/index',
							'tags' => $tag
						)) . '</strong>';
			}
		}
		foreach ( $data->torrents AS $torrent ) {
			echo ', ' . CHtml::link($torrent->getSeparateAttribute(),
					$torrent->getUrl()) . ($torrent->getIsNew() ? ' <span class="labelTorrent label label-info">' . Yii::t('torrentsModule.common',
						'новый') . '</span> ' : '');
		}
		?>
        </p>
    </div>
</div>