<?php
/**
 * @var \modules\torrents\models\TorrentGroup $model
 */
?>

<?php
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery.ui');
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/core.js');
//$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/cufon.js');
//$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/a_LCDNova_400.font.js');
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/player.js');
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/controls.js');
$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/css/ts-buttons.css');
$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/css/ts-controls-white.css');
$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/css/ts-custom.css');
$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/js/jquery.scrollText.js');

$jsData = [];
foreach ($model->torrents AS $torrent) {
    $jsData[] = [
        'url' => Yii::app()->createAbsoluteUrl('/torrents/default/download', ['id' => $torrent->getId()]),
        'hash' => $torrent->getInfoHash(),
        'title' => $torrent->getTitle()
    ];
}
$jsData = CJavaScript::encode(array_reverse($jsData));

$js = <<<JS
            function init() {
                var torrentsData = {$jsData};
                var useInternalControls = true;
                var tsDebug = false;
                var controls = new TorrentStream.Controls("tvplayer", {
                    style: useInternalControls ? "internal" : "ts-white-screen",
                    debug: tsDebug
                });
                try {
                    var player = new TorrentStream.Player(controls.getPluginContainer(), {
                        debug: tsDebug,
                        //useInternalPlaylist: true,
                        useInternalControls: useInternalControls,
                        bgColor: "#000000",
                        fontColor: "#ffffff",
                        onLoad: function () {
                            this.registerEventHandler(controls);
                            controls.attachPlayer(this);
                            try {
                                var p = this;
                                torrentsData.forEach(function(entry, i) {
                                    p.loadTorrent(entry.url, {autoplay: torrentsData.length == 1, async: true, name: entry.title});
                                });
                            }
                            catch (e) {
                                //console.log("init: " + e);
                            }
                        }
                    });
                }
                catch (e) {
                    //console.log(e);
                    controls.onSystemMessage(e);
                }
            }
            init();
JS;

$cs->registerCss(__FILE__, $css);
$cs->registerScript(__FILE__ . 'js', $js, CClientScript::POS_READY);
?>

<div class="tv-player-wrapper">
    <div class="tv-player" id="tvplayer">
        <div class="tv-loading"><?= Yii::t('torrentsModule.common', 'Идет загрузка плеера') ?></div>
    </div>
</div>