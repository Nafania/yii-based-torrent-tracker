<?php
/**
 * @var $models modules\torrents\models\Torrent[]
 */
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<channel>
<title><?php echo Yii::app()->config->get('base.siteName') ?></title>
<link><?php echo Yii::app()->createAbsoluteUrl('site/index') ?></link>
	<description><?php echo Yii::app()->config->get('base.defaultDescription') ?></description>
<!--<managingEditor>{MANAGING_EDITOR}</managingEditor>-->
<docs>http://blogs.law.harvard.edu/tech/rss</docs>
<!--<language>{LANGUAGE}</language>
<copyright>{COPYRIGHT}</copyright>-->
<lastBuildDate><?php echo gmdate('D, d M Y H:i:s') . ' GMT'; ?></lastBuildDate>
<image>
	<url><?php echo Yii::app()->createAbsoluteUrl('/') . Yii::app()->config->get('base.logoUrl') ?></url>
	<title><?php echo Yii::app()->config->get('base.siteName') ?></title>
	<link><?php echo Yii::app()->createAbsoluteUrl('site/index') ?></link>
	<width>88</width>
	<height>31</height>
</image>

<?php
foreach ( $models AS $model ) {
	$url = $model->getUrl();
	$link = Yii::app()->createAbsoluteUrl($url[0], array_splice($url, 1));

	$catUrl = $model->torrentGroup->category->getUrl();
?>

	<item>
		<title><?php echo $model->getTitle(); ?></title>
		<link><?php echo $link; ?></link>
		<guid isPermaLink="true"><?php echo $link; ?></guid>
		<pubDate><?php echo $model->getCtime('D, d M Y H:i:s') . ' GMT'; ?></pubDate>
		<description>
			<?php
			echo htmlspecialchars(CHtml::image($model->torrentGroup->getImageUrl(100, 0, true), $model->getTitle(), array('style' => 'width:100px'))) . '&lt;br&gt;';
			echo Yii::t('torrentsModule.common', 'Категория') . ': ' . $model->torrentGroup->category->getTitle() . '&lt;br&gt;';
			echo Yii::t('torrentsModule.common', 'Размер') . ': ' . SizeHelper::formatSize($model->getFilesSize()) . '&lt;br&gt;';
			echo Yii::t('torrentsModule.common', 'Время добавления') . ': ' . $model->getCtime('d.m.Y H:i:s') . '&lt;br&gt;';
			echo Yii::t('torrentsModule.common', 'Описание') . ': ' . $model->torrentGroup->getDescription();
			?>
		</description>
		<!--<author>{MANAGING_EDITOR} ({torrent_item.AUTHOR})</author>-->
		<category domain="<?php echo Yii::app()->createAbsoluteUrl($catUrl[0], array_splice($catUrl, 1)); ?>"><?php echo $model->torrentGroup->category->getTitle(); ?></category>
		<!--<comments>{torrent_item.U_REPLY}</comments>-->
	</item>

<?php } ?>

</channel>
</rss>
