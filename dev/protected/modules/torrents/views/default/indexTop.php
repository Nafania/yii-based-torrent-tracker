<?php
/**
 * @var integer $pageSize
 */

$cs = Yii::app()->getClientScript();
$cs->registerScript('pageSize', "$('#pageSize').change(function(){if ( typeof $.fn.yiiGridView != 'undefined' ) { $.fn.yiiGridView.update('torrents-grid',{ data:{pageSize: $('#pageSize').val() }}); } else { $.fn.yiiListView.update('torrents-list',{ data:{pageSize: $('#pageSize').val() }});}});");
$cs->registerLinkTag('alternate', 'application/rss+xml', Yii::app()->createAbsoluteUrl('torrents/rss.xml'));
?>

<?php

$this->widget('bootstrap.widgets.TbButtonGroup',
	array(
		'encodeLabel' => false,
		'htmlOptions' => array(
			'class' => 'pull-right'
		),
		'buttons'     => array(
			array(
				'label'       => CHtml::dropDownList('pageSize',
						$pageSize,
						array(
							10 => 10,
							20 => 20,
							30 => 30,
							40 => 40,
							50 => 50
						)),
				'url'         => '',
				'htmlOptions' => array(
					'title'    => Yii::t('torrentsModule.common', 'Количество торрентов на страницу'),
				    'id' => 'pageSizeBtn',
				),
			),
			array(
				'label'       => '<i class="icon-list"></i>',
				'url'         => Yii::app()->createUrl('/torrents/default/index', array('view' => 'list')),
				'active'      => Yii::app()->getUser()->getState('view', 'list') == 'list',
				'htmlOptions' => array(
					'title' => Yii::t('torrentsModule.common', 'Просмотр в виде списка'),
				)
			),
			array(
				'label'       => '<i class="icon-list-alt"></i>',
				'url'         => Yii::app()->createUrl('/torrents/default/index', array('view' => 'grid')),
				'active'      => Yii::app()->getUser()->getState('view', 'list') == 'grid',
				'htmlOptions' => array(
					'title' => Yii::t('torrentsModule.common', 'Просмотр в виде таблицы'),
				),
			),
		),
	));?>

<a id="listTop" class="clearfix"></a>