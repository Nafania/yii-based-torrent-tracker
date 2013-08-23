<?php
/* @var $this DefaultController */
/* @var $model Blog */
?>
<div class="media">
	<?php
	$img = CHtml::image($model->user->profile->getImageUrl(100, 100),
		$model->user->getName(),
		array(
		     'class' => 'media-object img-polaroid',
		     'style' => 'width:100px'
		));
	echo CHtml::link($img, $model->user->getUrl(), array('class' => 'pull-left'));
	?>

	<div class="media-body">
        <h1 class="media-heading"><?php echo $model->getTitle(); ?></h1>
        <p><?php echo $model->getDescription(); ?></p>
		<?php
		if ( Yii::app()->user->checkAccess('createPostInOwnBlog',
			array('ownerId' => $model->ownerId)) || Yii::app()->user->checkAccess('createPostInBlog')
		) {
		?>
		<p class="pull-right">
			<?php $this->widget('bootstrap.widgets.TbButton',
				array(
				     'buttonType'  => 'link',
				     'type'        => 'primary',
				     'label'       => 'Create post',
				     'url'         => array(
					     '/blogs/post/create', 'blogId' => $model->getId()
				     ),
				));
			?></p>
		<?php } ?>
    </div>
</div>
<hr />

<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'id'              => 'blogPostsListView',
	     'dataProvider'    => $postsProvider,
	     'itemView'        => 'application.modules.blogs.views.post._view',
	     'template'        => '{sorter} {items} {pager}',
	));
?>