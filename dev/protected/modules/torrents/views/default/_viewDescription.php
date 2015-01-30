<?php
/**
 * @var $this    modules\torrents\controllers\DefaultController
 * @var $model   modules\torrents\models\TorrentGroup
 * @var $torrent modules\torrents\models\Torrent
 */
?>
<dl class="dl-horizontal torrentView">
	<?php
	foreach ( $model->getEavAttributesWithKeys() AS $name => $value ) {
		echo '<dt>' . $name . '</dt>';
		echo '<dd>' . $value . '</dd>';
	}
	?>

	<dt><?php echo Yii::t('categoryModule.common', 'Категория'); ?></dt>
	<dd><?php echo CHtml::link($model->category->getTitle(),
			array(
				'/torrents/default/index',
				'category[]' => $model->category->getTitle()
			)); ?></dd>

	<?php
	if ($model->getTags()) {
	?>
	<dt><?php echo Yii::t('tagsModule.common', 'Теги'); ?></dt>
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

		<?php $this->widget('application.modules.reviews.widgets.ReviewWidget',
			array(
				'model'      => $model,
				'template'   => '<dt>{ratingTitle}</dt><dd>{ratingValue}</dd>'
			)); ?>


<dt><?php echo Yii::t('ratingsModule.common', 'Рейтинг'); ?></dt>
<dd>
	<?php $this->widget('application.modules.ratings.widgets.TorrentGroupRating',
		array(
			'model' => $model,
		)); ?>
</dd>

</dl>

<div class="accordion torrentsList">

<?php foreach ( $model->torrents(array('order' => 'ctime DESC')) AS $key => $torrent ) { ?>

<div class="accordion-group">
<div class="accordion-heading" id="torrent<?php echo $torrent->getId() ?>">
      <?php echo CHtml::link('<i class="icon-magnet"></i>',
     		$torrent->getMagnetUrl(),
     		array(
     			'class'       => 'btn btn-mini',
     			'data-toggle' => 'tooltip',
     			'title'       => Yii::t('torrentsModule',
     					'Magnet ссылка для {torrentName}',
     					array(
     						'{torrentName}' => $torrent->getTitle()
     					))
     		)) ?>
<?php echo CHtml::link('<i class="icon-download"></i>',
array(
	'/torrents/default/download',
	'id' => $torrent->getId()
),
array(
	'class'       => 'btn btn-mini',
	'data-toggle' => 'tooltip',
	'title'       => Yii::t('torrentsModule',
			'Скачать {torrentName}',
			array(
				'{torrentName}' => $torrent->getTitle()
			))
)) ?>
<?php if ( Yii::app()->getUser()->checkAccess('reports.default.create') ) {
	echo CHtml::link('<i class="icon-warning-sign"></i>',
		array(
			'/reports/default/create',
		),
		array(
			'class'       => 'btn btn-mini',
			'data-toggle' => 'tooltip',
			'data-model'  => $torrent->resolveClassName(),
			'data-id'     => $torrent->getId(),
			'data-action' => 'report',
			'title'       => Yii::t('reportsModule.common',
					'Пожаловаться на {torrentName}',
					array(
						'{torrentName}' => $torrent->getTitle()
					))
		));
} ?>
<?php
echo CHtml::link('<i class="icon-comment"></i>',
	'#',
	array(
		'class'             => 'btn btn-mini',
		'data-toggle'       => 'tooltip',
		'data-comments-for' => $torrent->getId(),
		'data-action'       => 'report',
		'title'             => Yii::t('torrentsModule.common',
				'Смотреть комментарии только для {torrentName}',
				array(
					'{torrentName}' => $torrent->getTitle()
				))
	));
?>
<?php
echo CHtml::link('<i class="icon-file"></i>',
	array(
		'/torrents/default/fileList'
	),
	array(
		'class'       => 'btn btn-mini',
		'data-toggle' => 'tooltip',
		'data-action' => 'fileList',
		'data-id'     => $torrent->getId(),
		'title'       => Yii::t('torrentsModule.common',
				'Смотреть список файлов для {torrentName}',
				array(
					'{torrentName}' => $torrent->getTitle()
				))
	));
?>
<?php if ( Yii::app()->getUser()->checkAccess('files.default.index') && $torrent->files ) {
	Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/jMyCarousel/jMyCarousel.js');
    Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('files')->getAssetsUrl() . '/js/filesViewGallery.js');

	echo CHtml::link('<i class="icon-picture"></i>',
		array(
			'/files/default/index'
		),
		array(
			'class'       => 'btn btn-mini',
			'data-toggle' => 'tooltip',
			'data-action' => 'filesList',
			'data-id'     => $torrent->getId(),
			'data-model'  => $torrent->resolveClassName(),
			'title'       => Yii::t('filesModule.common',
					'Смотреть скриншоты для {torrentName}',
					array(
						'{torrentName}' => $torrent->getTitle()
					))
		));

} ?>
<?php
if ( Yii::app()->user->checkAccess('updateOwnTorrent',
		array('model' => $torrent)) || Yii::app()->user->checkAccess('updateTorrent')
) {

	echo CHtml::link('<i class="icon-edit"></i>',
		array(
			'/torrents/default/updateTorrent',
			'id' => $torrent->getId()
		),
		array(
			'class'       => 'btn btn-mini',
			'data-toggle' => 'tooltip',
			'title'       => Yii::t('torrentsModule.common',
					'Редактировать {torrentName}',
					array(
						'{torrentName}' => $torrent->getTitle()
					))
		));
	?>
<?php
}

if ( Yii::app()->getUser()->checkAccess('torrents.default.deleteTorrent') ) {

	echo CHtml::link('<i class="icon-trash"></i>',
		array(
			'/torrents/default/deleteTorrent',
			'id' => $torrent->getId(),
		),
		array(
			'class'       => 'btn btn-mini torrentDelete',
			'data-toggle' => 'tooltip',
			'title'       => Yii::t('torrentsModule.common',
					'Удалить торрент {torrentName}',
					array(
						'{torrentName}' => $torrent->getTitle()
					))
		));


} ?>

<a class="accordion-toggle" data-toggle="collapse" href="#torrent<?php echo $torrent->getId() ?>" data-target="#collapse<?php echo $torrent->getId() ?>">
          <span title="<?= Yii::t('torrentsModule.common', 'Посмотреть полную информацию о торренте {torrentName}', array('{torrentName}' => $torrent->getTitle())) ?>" data-toggle="tooltip"><?php echo $torrent->getSeparateAttribute() ?></span>
      </a>

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

<span><i class="icon-upload" data-toggle="tooltip" data-placement="top" title="<?php echo $torrent->getAttributeLabel('seeders') ?>"></i> <?php echo $torrent->getSeeders(); ?>
	<i class="icon-download" data-toggle="tooltip" data-placement="top" title="<?php echo $torrent->getAttributeLabel('leechers') ?>"></i> <?php echo $torrent->getLeechers(); ?></span>

<span class="divider-vertical">|</span>

<span><?php echo $torrent->getAttributeLabel('downloads') ?>
	: <?php echo $torrent->getDownloads(); ?></span>
</div>

<div id="collapse<?php echo $torrent->getId() ?>" class="accordion-body collapse">
              <div class="accordion-inner">
                  <dl class="dl-horizontal">
                   <?php
                   if ( $torrent->user && Yii::app()->getUser()->checkAccess('canViewTorrentOwner') ) {
                    echo '<dt>' . Yii::t('torrentsModule.common', 'Добавил') . '</dt>';
                    echo '<dd>' . CHtml::link($torrent->user->getName(), $torrent->user->getUrl()) . '</dd>';
                   }
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

<div id="filesList<?php echo $torrent->getId() ?>" class="accordion-body collapse">
              <div class="accordion-inner"></div>
          </div>
</div>

<?php } ?>
</div>