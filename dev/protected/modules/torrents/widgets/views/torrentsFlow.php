<?php
/**
 * @var $torrentsGroup TorrentGroup
 * @var $tabs          array
 */
?>

<?php ?>


<section class="module newTorrents">
    <h3 class="moduleHeader"><?php echo Yii::t('torrentsModule.common', 'Новые торренты') ?></h3>
	<?php $this->widget('ext.bootstrap.widgets.TbTabs',
		array(
		     'tabs'        => $tabs,
		));
	?>
</section>