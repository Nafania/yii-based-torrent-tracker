<?php
/**
 * @var $this    modules\torrents\controllers\DefaultController
 * @var $model   modules\torrents\models\TorrentGroup
 * @var $torrent modules\torrents\models\Torrent
 * @var $torrentStreamModel modules\torrents\models\TorrentstreamCategory
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/torrents.js');
$cs->registerCssFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.css');
$cs->registerScriptFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.js');
?>

    <h1><?php echo $model->getTitle() ?></h1>

    <div class="row-fluid">
        <div class="span3">
            <?php
            $img = CHtml::image($model->getImageUrl(500, 0), $model->getTitle());
            echo CHtml::link($img,
                $model->getImageUrl(),
                array(
                    'class' => 'fancybox img-polaroid torrentImage',
                    'rel' => 'group'
                ));
            ?>
            <?php
            $this->widget('application.modules.torrents.widgets.TorrentGroupMenu',
                array(
                    'model' => $model
                ));
            ?>
            <?php $this->widget('application.modules.advertisement.widgets.AdsBlockWidget',
                array(
                    'systemName' => 'underTorrentImage',
                ))
            ?>
        </div>

        <div class="span9">

            <?php $this->widget('application.modules.advertisement.widgets.AdsBlockWidget',
                array(
                    'systemName' => 'topTorrentView',
                    'model' => $model
                ));

            if ($torrentStreamModel) {

                $this->widget(
                    'bootstrap.widgets.TbTabs',
                    array(
                        'type' => 'tabs',
                        'tabs' => array(
                            array(
                                'id' => 'description',
                                'label' => Yii::t('torrentsModule.common', 'Описание'),
                                'url' => Yii::app()->createUrl($model->getUrl()[0], array_slice($model->getUrl(), 1)),
                                'content' => $this->renderPartial('_viewDescription', array('model' => $model), true),
                                'active' => true
                            ),
                            array(
                                'label' => Yii::t('torrentsModule.common', '{torrentStreamTitle} "{groupName}"', ['{torrentStreamTitle}' => $torrentStreamModel->getTitle(), '{groupName}' => $model->getTitle()]),
                                'id' => 'watchOnline',
                                'url' => Yii::app()->createUrl('/torrents/default/watchOnline', ['id' => $model->getId(), 'title' => $model->getSlugTitle()]),
                            ),
                        )
                    )
                );

            } else {
                $this->renderPartial('_viewDescription', array('model' => $model));
            }

            $this->widget('application.modules.comments.widgets.CommentsTreeWidget',
                array(
                    'model' => $model,
                )); ?>

            <?php $this->widget('application.modules.comments.widgets.AnswerWidget',
                array(
                    'model' => $model,
                    'torrents' => $model->torrents
                )); ?>
        </div>

    </div>
<?php $this->widget('application.modules.torrents.widgets.AdultsWarning',
    array(
        'model' => $model
    )); ?>
<?php $this->widget('application.modules.reports.widgets.ReportModal'); ?>