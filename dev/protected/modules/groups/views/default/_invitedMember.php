<?php
/* @var $this DefaultController */
/* @var $data User */
/* @var $group Group */
/* @var $status integer */
?>
<div class="media commentContainer">

	<?php
	$img = $data->profile->getImageUrl(80, 80);
	$alt = $data->getName();
	$url = $data->getUrl();
	echo CHtml::link(CHtml::image($img,
			$alt,
			array(
			     'class'  => 'media-object',
			     'width'  => '80',
			     'height' => '80'
			)),
		$url,
		array('class' => 'pull-left'));
	?>

	<div class="media-body">
        <h3 class="media-heading">
	        <?php
	        echo CHtml::link($data->getName(), $data->getUrl());
	        ?>
	    </h3>
	</div>

</div>
<hr />

