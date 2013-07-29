<?php
/**
 * @var $model TorrentGroup
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/fancyapps-fancyBox/source/jquery.fancybox.css');
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/fancyapps-fancyBox/source/jquery.fancybox.js');
$cs->registerScript(__FILE__ . 'fancybox', '$(".fancybox").fancybox();', CClientScript::POS_LOAD);
$cs->registerScript(__FILE__ . 'accordition', '  location.hash && $(location.hash + ".collapse").collapse("show");', CClientScript::POS_LOAD)
?>
<h1><?php echo $model->getTitle() ?></h1>
<div class="row-fluid">
	<div class="span3">
	<?php   $img = CHtml::image($model->getImageUrl(365, 500), $model->getTitle(), array('class' => 'img-polaroid'));
		echo CHtml::link($img,
			$model->getImageUrl(),
			array(
			     'class' => 'fancybox',
			     'rel'   => 'group'
			));
		?>
	</div>

	<div class="span9">
		<dl class="dl-horizontal">
			<?php
			foreach ( $model->getEavAttributesWithKeys() AS $name => $value ) {
				echo '<dt>' . $name . '</dt>';
				echo '<dd>' . $value . '</dd>';
			}
			?>

			<dt><?php echo Yii::t('tagsModule.common', 'Tags'); ?></dt>
			<dd>
			<?php
				$tags = '';
				foreach ( $model->getTags() AS $key => $tag ) {
					$tags .= ($tags ? ', ' : '') . CHtml::link($tag);
				}
				echo $tags;
				?>
			</dd>
		</dl>

		<div class="accordion">

		<?php foreach ( $model->torrents(array('order' => 'ctime DESC')) AS $key => $torrent ) { ?>

			<div class="accordion-group">
                <div class="accordion-heading">
	                <?php echo CHtml::link('<i class="icon-download"></i>',
		                array(
		                     '/torrents/default/download',
		                     'id' => $torrent->getId()
		                ),
		                array(
		                     'class'               => 'btn',
		                     'data-toggle'         => 'tooltip',
		                     'data-original-title' => Yii::t('torrentsModule',
			                     'Скачать {torrentName}',
			                     array('{torrentName}' => $model->getTitle()))
		                )) ?>
	                <a href="#" class="btn" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule',
		                'Пожаловаться на {torrentName}',
		                array('{torrentName}' => $model->getTitle())); ?>"><i class="icon-warning-sign"></i></a>
                    <a href="#" class="btn" data-comments-for="<?php echo $torrent->getId() ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule',
	                    'Смотреть комментарии для {torrentName}',
	                    array('{torrentName}' => $model->getTitle())); ?>"><i class="icon-comment"></i></a>

	                <a class="accordion-toggle" data-toggle="collapse" href="#collapse<?php echo md5($torrent->getSeparateAttribute()) ?>"><?php echo $torrent->getSeparateAttribute() ?></a>

                    <span class="divider-vertical">|</span>

                    <span><?php echo $torrent->getAttributeLabel('size') ?>: <abbr title="<?php echo Yii::t('torrentsModule.common',
		                    '{size} bytes',
		                    array('{size}' => $torrent->getSize())); ?>"><?php echo $torrent->getSize(true); ?></abbr></span>

                    <span class="divider-vertical">|</span>

                    <span><?php echo $torrent->getAttributeLabel('ctime') ?>: <abbr title="<?php echo $torrent->getCtime('d.m.Y H:i'); ?>"><?php echo TimeHelper::timeAgoInWords($torrent->getCtime()); ?></abbr></span>

                    <span class="divider-vertical">|</span>

                    <span><?php echo Yii::t('torrentsModule.common', 'Peers') ?>: <i class="icon-upload" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $torrent->getAttributeLabel('seeders') ?>"></i> <?php echo $torrent->getSeeders(); ?>
	                    <i class="icon-download" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $torrent->getAttributeLabel('leechers') ?>"></i> <?php echo $torrent->getLeechers(); ?></span>

                    <span class="divider-vertical">|</span>

                    <span><?php echo $torrent->getAttributeLabel('downloads') ?>: <?php echo $torrent->getDownloads(); ?></span>
                </div>

            <div id="collapse<?php echo md5($torrent->getSeparateAttribute()) ?>" class="accordion-body collapse">
                <div class="accordion-inner">
                    <dl class="dl-horizontal">
	                    <?php
	                    foreach ( $torrent->getEavAttributesWithKeys() AS $name => $value ) {
		                    echo '<dt>' . $name . '</dt>';
		                    echo '<dd>' . $value . '</dd>';
	                    }
	                    ?>
                    </dl>
                </div>
            </div>
        </div>
			<?php } ?>
	</div>
</div>
	</div>