<?php
$cs = Yii::app()->getClientScript();
$cs->registerCssFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/jMyCarousel/css/style.css');
//$cs->registerScriptFile(Yii::app()->getModule('torrents')->getAssetsUrl() . '/jMyCarousel/jMyCarousel.js');
$cs->registerScript('filesCarousel',
	"$('#filesCarousel').jMyCarousel({height: 120, width:158, visible: '100%',eltByElt: true, evtStart:'click',evtStop:'click', mouseWheel: true});");
?>

<div class="jMyCarousel" id="filesCarousel">
	<ul>
	<?php
	foreach ( $files AS $file ) {
		$alt = $file->getTitle();
		$img = CHtml::image($file->getFileUrl(),
			$alt,
			array(
				'style' => 'height:105px',
			));

		echo '<li class="fileListItem">' . CHtml::link($img,
				$file->getFileUrl(),
				array(
					'class' => 'fancybox',
					'rel'   => 'filesGroup'
				)) . '</li>';
	}?>
</ul>
</div>
