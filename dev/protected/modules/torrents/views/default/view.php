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
$cs->registerScript(__FILE__ . 'accordion',
	'  location.hash && $(location.hash + ".collapse").collapse("show");',
	CClientScript::POS_LOAD)
?>
	<h1><?php echo $model->getTitle() ?></h1>
	<div class="row-fluid">
	<div class="span3">
	<?php   $img = CHtml::image($model->getImageUrl(300, 0), $model->getTitle(), array('class' => 'img-polaroid'));
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
				if ( $model->getTags() ) {
					$tags = '';
					foreach ( $model->getTags() AS $key => $tag ) {
						$tags .= ($tags ? ', ' : '') . CHtml::link($tag,
							array(
							     '/torrents/default/index',
							     'tags' => $tag
							));
					}
					echo $tags;
				}
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
			                     'Скачать {torrentGroupName} / {torrentName}',
			                     array(
			                          '{torrentGroupName}' => $model->getTitle(),
			                          '{torrentName}'      => $torrent->getSeparateAttribute()
			                     ))
		                )) ?>
	                <a href="<?php echo Yii::app()->createUrl('/reports/default/create/',
		                array(
		                     'modelName' => get_class($torrent),
		                     'modelId'   => $torrent->getId()
		                )); ?>" data-action="report" class="btn" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('reportsModule.common',
		                'Пожаловаться на {torrentGroupName} / {torrentName}',
		                array(
		                     '{torrentGroupName}' => $model->getTitle(),
		                     '{torrentName}'      => $torrent->getSeparateAttribute()
		                )); ?>"><i class="icon-warning-sign"></i></a>
	                <a href="#" class="btn" data-comments-for="<?php echo $torrent->getId() ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule',
		                'Смотреть комментарии только для {torrentGroupName} / {torrentName}',
		                array(
		                     '{torrentGroupName}' => $model->getTitle(),
		                     '{torrentName}'      => $torrent->getSeparateAttribute()
		                )); ?>"><i class="icon-comment"></i></a>

	                <?php
	                if ( Yii::app()->getUser()->checkAccess('torrentsUpdate') ) {
		                ?>
		                <a href="<?php echo Yii::app()->createUrl('/torrents/default/updateTorrent/',
			                array(
			                     'id'   => $torrent->getId()
			                )); ?>" class="btn" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule.common',
			                'Редактировать {torrentGroupName} / {torrentName}',
			                array(
			                     '{torrentGroupName}' => $model->getTitle(),
			                     '{torrentName}'      => $torrent->getSeparateAttribute()
			                )); ?>"><i class="icon-edit"></i></a>
		                <?php
	                }
	                ?>

	                <a class="accordion-toggle" data-toggle="collapse" href="#collapse<?php echo $torrent->getId() ?>"><?php echo $torrent->getSeparateAttribute() ?></a>

                    <span class="divider-vertical">|</span>

                    <span><?php echo $torrent->getAttributeLabel('size') ?>
	                    : <abbr title="<?php echo Yii::t('torrentsModule.common',
		                    '{size} bytes',
		                    array('{size}' => $torrent->getSize())); ?>"><?php echo $torrent->getSize(true); ?></abbr></span>

                    <span class="divider-vertical">|</span>

                    <span><?php echo $torrent->getAttributeLabel('ctime') ?>
	                    : <abbr title="<?php echo $torrent->getCtime('d.m.Y H:i'); ?>"><?php echo TimeHelper::timeAgoInWords($torrent->getCtime()); ?></abbr></span>

                    <span class="divider-vertical">|</span>

                    <span><?php echo Yii::t('torrentsModule.common', 'Peers') ?>
	                    : <i class="icon-upload" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $torrent->getAttributeLabel('seeders') ?>"></i> <?php echo $torrent->getSeeders(); ?>
	                    <i class="icon-download" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $torrent->getAttributeLabel('leechers') ?>"></i> <?php echo $torrent->getLeechers(); ?></span>

                    <span class="divider-vertical">|</span>

                    <span><?php echo $torrent->getAttributeLabel('downloads') ?>
	                    : <?php echo $torrent->getDownloads(); ?></span>
                </div>

            <div id="collapse<?php echo $torrent->getId() ?>" class="accordion-body collapse">
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
<div class="commentsBlock">
<?php $this->widget('application.modules.comments.widgets.CommentsTreeWidget',
		array(
		     'model' => $model,
		)); ?>
</div>
		<?php $this->widget('application.modules.comments.widgets.AnswerWidget',
			array(
			     'model'    => $model,
			     'torrents' => $model->torrents
			)); ?>
</div>
</div>

<?php $this->widget('application.modules.reports.widgets.ReportModal'); ?>