<?php
/**
 * @var $tabs          array
 */
?>

<section class="module newTorrents">
    <h3 class="moduleHeader"><?php echo Yii::t('torrentsModule.common', 'Лучшие торренты') ?></h3>
	<?php $this->widget('ext.bootstrap.widgets.TbTabs',
		array(
		     'tabs'        => $tabs,
		));
	?>
</section>