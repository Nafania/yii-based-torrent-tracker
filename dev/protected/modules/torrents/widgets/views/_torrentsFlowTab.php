<?php
$cs = Yii::app()->getClientScript();
$cs->registerScript('Carousel' . $catId, "$('#Carousel" . $catId . "').jMyCarousel({visible: '100%',eltByElt: true, evtStart:'click',evtStop:'click'});");
?>

<div class="jMyCarousel" id="Carousel<?php echo $catId ?>">
	<ul>
	<?php
		foreach ( $torrentsGroup AS $group ) {
			$alt = $group->getTitle();
			$url = CHtml::link($group->getTitle(), $group->getUrl());
			$img = CHtml::image($group->getImageUrl(140, 200),
				$alt,
				array(
				     'width' => 140,
				     'height' => 200,
				));

			echo '<li class="torrentItem"><h3>' . $url . '</h3>' . CHtml::link($img, $group->getUrl()) . '</li>';
	}?>
</ul>
</div>
