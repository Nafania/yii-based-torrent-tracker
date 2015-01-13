<?php
if (!empty($pageSize)) {
    $this->renderPartial('application.modules.torrents.views.default.indexTop', array('pageSize' => $pageSize));
}

$groupGridColumns = array(
    array(
        'name' => '',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo CHtml::link('<i class="icon-magnet"></i>',
                    $data->getMagnetUrl(),
                    array(
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('torrentsModule',
                                'Magnet ссылка для {torrentName}',
                                array(
                                    '{torrentName}' => $data->getTitle()
                                ))
                    ));
                echo '<br>';
                echo CHtml::link('<i class="icon-download"></i>',
                    array(
                        '/torrents/default/download',
                        'id' => $data->getId()
                    ),
                    array(
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('torrentsModule',
                                'Скачать {torrentName}',
                                array(
                                    '{torrentName}' => $data->getTitle()
                                ))
                    ));
            },
        'htmlOptions' => array(
            'width' => '18px'
        )
    ),
    array(
        'name' => Yii::t('torrentsModule.common', 'Тип'),
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo CHtml::link(CHtml::image($data->torrentGroup->category->getImageUrl(50),
                        $data->torrentGroup->category->getTitle(),
                        array('style' => 'width:50px')),
                    array(
                        '',
                        'category[]' => $data->torrentGroup->category->getTitle()
                    ));
            },
        'htmlOptions' => array(
            'width' => '66px'
        )
    ),
    array(
        'name' => 'title',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo '<strong>' . CHtml::link($data->getTitle(),
                        $data->getUrl()) . ($data->getIsNew() ? ' <span class="labelTorrent label label-info">' . Yii::t('torrentsModule.common',
                            'новый') . '</span> ' : '') . '</strong><br>' . CHtml::link($data->torrentGroup->category->getTitle(),
                        array(
                            '',
                            'category[]' => $data->torrentGroup->category->getTitle()
                        ));
                if ($tags = $data->getTags()) {
                    foreach ($tags AS $tag) {
                        echo ', ' . CHtml::link($tag,
                                array(
                                    '/torrents/default/index',
                                    'tags' => $tag
                                ));
                    }
                }
            }
    ),
    array(
        'name' => 'ctime',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo $data->getCtime('d.m.Y H:i:s');
            },
        'htmlOptions' => array(
            'width' => '70px'
        )
    ),
    array(
        'name' => Yii::t('torrentsModule.common', 'Файлы'),
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo $data->getFilesCount();
            },
        'htmlOptions' => array(
            'width' => '45px'
        )
    ),
    array(
        'header' => Yii::t('torrentsModule.common', 'Ком.'),
        'name' => 'commentsCount',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo CHtml::link(($data->torrentGroup->commentsCount ? $data->torrentGroup->commentsCount : 0),
                    CMap::mergeArray($data->torrentGroup->getUrl(), array('#' => 'comments')));
            },
        'sortable' => true,
        'htmlOptions' => array(
            'width' => '35px'
        )
    ),
    array(
        'header' => Yii::t('torrentsModule.common', 'Рейтинг'),
        'name' => 'rating',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                Yii::app()->getController()->widget('application.modules.ratings.widgets.TorrentGroupRating',
                    array(
                        'model' => $data->torrentGroup,
                        'onlyBar' => true,
                    ));
            },
        'sortable' => true,
        'htmlOptions' => array(
            'width' => '60px'
        )
    ),
    array(
        'name' => 'size',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo $data->getSize(true);
            },
        'htmlOptions' => array(
            'width' => '60px'
        )
    ),
    array(
        'name' => 'downloads',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo $data->getDownloads();
            },
        'htmlOptions' => array(
            'width' => '50px'
        )
    ),
    array(
        'name' => 'seeders',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo $data->getSeeders();
            },
        'htmlOptions' => array(
            'width' => '50px'
        )
    ),
    array(
        'name' => 'leechers',
        'type' => 'raw',
        'value' => function (\modules\torrents\models\Torrent $data) {
                echo $data->getLeechers();
            },
        'htmlOptions' => array(
            'width' => '50px'
        )
    ),
);


$groupGridColumns[] = array(
    'name' => 'groupCtime',
    'value' => function($data) { return $data->getCtime('d.m.Y');},
    'headerHtmlOptions' => array('style' => 'display:none'),
    'htmlOptions' => array('style' => 'display:none')
);

if ( Yii::app()->getUser()->checkAccess('canViewTorrentOwner') ) {
    $groupGridColumns[] = [
        'name' => 'uid',
        'value' => function($data) { return ( $data->user ? CHtml::link($data->user->getName(), $data->user->getUrl()) : '-' );},
        'type' => 'raw'
    ];
}

?>

<?php $this->widget('bootstrap.widgets.TbGroupGridView',
    array(
        'dataProvider' => $dataProvider,
        'type' => 'striped bordered',
        'template' => "{items}\n{pager}",
        'extraRowColumns' => ['groupCtime'],
        'extraRowExpression' => function($data) { return '<abbr title="' . $data->getCtime('d.m.Y') . '" class="torrentGridHeader">' . Yii::t('torrentsModule.common', 'Добавлено') . ' ' . TimeHelper::timeAgoInWords($data->getCtime()) . '</abbr>';},
        'enableHistory' => true,
        'enableSorting' => true,
        'id' => 'torrents-grid',
        'columns' => $groupGridColumns,
    ));