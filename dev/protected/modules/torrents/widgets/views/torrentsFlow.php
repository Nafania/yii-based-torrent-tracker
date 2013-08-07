<?php
/**
 * @var $torrentsGroup TorrentGroup
 * @var $tabs array
 */
?>

<?php ?>


<section class="module newTorrents">
    <h3 class="moduleHeader"><?php echo Yii::t('torrentsModule.common', 'New torrents') ?></h3>
	<?php $this->widget('application.modules.torrents.widgets.EJuiTabs',
		array(
		     'tabs' => $tabs,
		     'options'=>array(
		         'collapsible'=>false,
			     'cookie' => array(
				     'expires' => 30
			     )
		     ),
		));
	?>
</section>