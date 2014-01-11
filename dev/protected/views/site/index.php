<?php
$this->widget('application.modules.blogs.widgets.NewsBlock');

$this->widget('application.modules.torrents.widgets.TorrentsFlow');

?>

</div>

<div class="row-fluid" style="margin-top: 20px;">

<div class="span4 ">
	<section class="module">

	    <h3 class="moduleHeader"><?php echo Yii::t('commentsModule.common', 'Последние комментарии') ?></h3>

		<div class="moduleContainer latestComments accordion-group">
			<?php $this->widget('application.modules.comments.widgets.LatestComments', array(
				'limit' => 7
			)); ?>
		</div>
	</section>
</div>



<div class="span4">
	<section class="module">

	    <h3 class="moduleHeader"><?php echo Yii::t('torrentsModule.common', 'Популярные торренты') ?></h3>

		<div class="moduleContainer latestTorrents accordion-group">
			<?php $this->widget('application.modules.torrents.widgets.LatestTorrents', array(
				'limit' => 10
			)); ?>
		</div>
	</section>
</div>

<div class="span4">
	<section class="module">

	    <h3 class="moduleHeader"><?php echo Yii::t('blogsModule.common', 'Новое в блогах') ?></h3>

		<div class="moduleContainer latestPosts">
			<?php $this->widget('application.modules.blogs.widgets.LatestPosts', array(
				'limit' => 5
			)); ?>
		</div>
	</section>
</div>
