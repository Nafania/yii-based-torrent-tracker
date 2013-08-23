<?php
/**
 * @var $model TorrentGroup
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/torrents.js');
$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/fancyapps-fancyBox/source/jquery.fancybox.css');
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/fancyapps-fancyBox/source/jquery.fancybox.js');
?>
	<h1>
	<?php echo $model->getTitle() ?>
</h1>

	<div class="row-fluid">
	<div class="span3">
	<?php
		$img = CHtml::image($model->getImageUrl(290, 0), $model->getTitle(), array('class' => 'img-polaroid'));
		echo CHtml::link($img,
			$model->getImageUrl(),
			array(
			     'class' => 'fancybox',
			     'rel'   => 'group'
			));
		?>
		<div class="span12 text-center torrentGroupOperations">
			<?php if ( Yii::app()->getUser()->checkAccess('torrents.default.createTorrent') ) { ?>
				<a href="<?php echo Yii::app()->createUrl('/torrents/default/createTorrent',
					array('gId' => $model->getId())) ?>" class="btn pull-left" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule',
					'Добавить торрент в группу') ?>"><i class="icon-upload"></i></a>
			<?php } ?>

			<?php if ( Yii::app()->getUser()->checkAccess('torrents.default.updateGroup') ) { ?>
				<a href="<?php echo Yii::app()->createUrl('/torrents/default/updateGroup',
					array('id' => $model->getId())) ?>" class="btn" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule',
					'Редактировать группу торрентов') ?>"><i class="icon-edit"></i></a>
			<?php } ?>

			<?php if ( Yii::app()->getUser()->checkAccess('torrents.default.delete') ) {
				$cs->registerScript('torrentDelete',
					"$('a.torrentGroupDelete, a.torrentDelete').on('click', function(e) {
											e.preventDefault();
											if ( confirm(" . CJavaScript::encode(Yii::t('torrentsModule.common',
						'Вы уверены, что хотите удалить этот элемент?')) . ") ) {
						$(this).addClass('load');
				        $.post($(this).attr('href'), {csrf: " . CJavaScript::encode(Yii::app()->getRequest()->getCsrfToken()) . "}, function(data) {
				          if ( data.data.location ) {
				            window.location.replace(data.data.location);
				          }
				        }, 'json');}
				});");
				?>
				<a href="<?php echo Yii::app()->createUrl('/torrents/default/delete',
					array('id' => $model->getId())) ?>" class="btn pull-right torrentGroupDelete" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule',
					'Удалить группу торрентов') ?>"><i class="icon-trash"></i></a>
			<?php } ?>
		</div>

	</div>

	<div class="span9">
	<dl class="dl-horizontal">
			<?php
		foreach ( $model->getEavAttributesWithKeys() AS $name => $value ) {
			echo '<dt>' . $name . '</dt>';
			echo '<dd>' . $value . '</dd>';
		}
		?>


		<?php
		if ( $model->getTags() ) { ?>
		<dt><?php echo Yii::t('tagsModule.common', 'Tags'); ?></dt>
						<dd>
				<?php
							$tags = '';
							foreach ( $model->getTags() AS $key => $tag ) {
								$tags .= ($tags ? ', ' : '') . CHtml::link($tag,
									array(
									     '/torrents/default/index',
									     'tags' => $tag
									));
							}
							echo $tags . '</dd>';
							}
							?>


		<dt><?php echo Yii::t('ratingsModule.common', 'Rating'); ?></dt>
		<dd>
			<?php $this->widget('application.modules.ratings.widgets.TorrentGroupRating',
				array(
				     'model' => $model,
				)); ?>
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
			     'class'               => 'btn btn-mini',
			     'data-toggle'         => 'tooltip',
			     'data-original-title' => Yii::t('torrentsModule',
				     'Скачать {torrentName}',
				     array(
				          '{torrentName}' => $torrent->getTitle()
				     ))
			)) ?>

		<?php if ( Yii::app()->getUser()->checkAccess('reports.default.create') ) { ?>

			<a href="<?php echo Yii::app()->createUrl('/reports/default/create/',
				array(
				     'modelName' => get_class($torrent),
				     'modelId'   => $torrent->getId()
				)); ?>" data-action="report" class="btn btn-mini" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('reportsModule.common',
				'Пожаловаться на {torrentName}',
				array(
				     '{torrentName}' => $torrent->getTitle()
				)); ?>"><i class="icon-warning-sign"></i></a>

		<?php } ?>

		<a href="#" class="btn btn-mini" data-comments-for="<?php echo $torrent->getId() ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule.common',
			'Смотреть комментарии только для {torrentName}',
			array(
			     '{torrentName}' => $torrent->getTitle()
			)); ?>"><i class="icon-comment"></i></a>

		<a href="<?php echo Yii::app()->createUrl('/torrents/default/fileList') ?>" class="btn btn-mini" data-action="fileList" data-id="<?php echo $torrent->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule.common',
			'Смотреть список файлов для {torrentName}',
			array(
			     '{torrentName}' => $torrent->getTitle()
			)); ?>"><i class="icon-file"></i></a>

		<?php
		if ( Yii::app()->getUser()->checkAccess('torrentsUpdate') ) {
			?>
			<a href="<?php echo Yii::app()->createUrl('/torrents/default/updateTorrent/',
				array(
				     'id' => $torrent->getId()
				)); ?>" class="btn btn-mini" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule.common',
				'Редактировать {torrentName}',
				array(
				     '{torrentName}' => $torrent->getTitle()
				)); ?>"><i class="icon-edit"></i></a>
		<?php
		}
		if ( Yii::app()->getUser()->checkAccess('torrents.default.deleteTorrent') ) {
			?>
			<a href="<?php echo Yii::app()->createUrl('/torrents/default/deleteTorrent',
				array('id' => $torrent->getId())) ?>" class="btn btn-mini torrentDelete" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('torrentsModule.common',
				'Удалить торрент {torrentName}',
				array(
				     '{torrentName}' => $torrent->getTitle()
				)); ?>"><i class="icon-trash"></i></a>
		<?php } ?>

		<a class="accordion-toggle" data-toggle="collapse" href="#collapse<?php echo $torrent->getId() ?>"><?php echo $torrent->getSeparateAttribute() ?></a>

		<span class="divider-vertical">|</span>

		<span><abbr title="<?php echo Yii::t('torrentsModule.common',
				'Размер: {size} bytes',
				array('{size}' => $torrent->getSize())); ?>"><?php echo $torrent->getSize(true); ?></abbr></span>

		<span class="divider-vertical">|</span>

		<span><abbr title="<?php echo Yii::t('torrentsModule.common',
				'Добавлено: {date}',
				array(
				     '{date}' => $torrent->getCtime('d.m.Y H:i')
				)) ?>"><?php echo TimeHelper::timeAgoInWords($torrent->getCtime()); ?></abbr></span>

		<span class="divider-vertical">|</span>

		<span><i class="icon-upload" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $torrent->getAttributeLabel('seeders') ?>"></i> <?php echo $torrent->getSeeders(); ?>
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
		<div id="fileList<?php echo $torrent->getId() ?>" class="accordion-body collapse">
                <div class="accordion-inner"></div>
            </div>
		</div>
		<?php } ?>
	</div>

	<?php $this->widget('application.modules.comments.widgets.CommentsTreeWidget',
			array(
			     'model' => $model,
			)); ?>

		<?php $this->widget('application.modules.comments.widgets.AnswerWidget',
			array(
			     'model'    => $model,
			     'torrents' => $model->torrents
			)); ?>
	</div>
	</div>

<?php $this->widget('application.modules.reports.widgets.ReportModal'); ?>