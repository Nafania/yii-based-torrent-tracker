<?php
/**
 * @var $data Blog
 */
?>
<div class="media">
	<?php
	$img = CHtml::image($data->user->profile->getImageUrl(100, 100),
		$data->user->getName(),
		array(
		     'class' => 'media-object img-polaroid',
		     'style' => 'width:100px'
		));
	echo CHtml::link($img, $data->user->getUrl(), array('class' => 'pull-left'));
	?>

	<div class="media-body">
        <h2 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h2>

        <p><?php echo StringHelper::cutStr($data->getDescription()); ?></p>

		<div class="pull-right">
			<?php
			if ( Yii::app()->user->checkAccess('createPostInOwnBlog',
				array('ownerId' => $data->ownerId)) || Yii::app()->user->checkAccess('createPostInBlog')
			) {
			?>
				<?php $this->widget('bootstrap.widgets.TbButton',
					array(
					     'buttonType'  => 'link',
					     'type'        => 'primary',
					     'label'       => 'Create post',
					     'url'         => array(
						     '/blogs/post/create', 'blogId' => $data->getId()
					     ),
					));
				?>
				&nbsp;
			<?php } ?>
			<?php
			if ( Yii::app()->user->checkAccess('editOwnBlog',
				array('ownerId' => $data->ownerId)) || Yii::app()->user->checkAccess('editBlog')
			) {
			?>
		<?php $this->widget('bootstrap.widgets.TbButton',
				array(
				     'buttonType'  => 'submitLink',
				     'type'        => 'primary',
				     'label'       => 'Edit',
				     'url'         => array(
					     '/blogs/default/update',
					     'id' => $data->getId()
				     ),
				     'htmlOptions' => array(
					     'csrf' => true,
					     'href' => array(
						     '/blogs/default/update',
						     'id' => $data->getId()
					     ),
				     )
				));
			?>
				&nbsp;
			<?php } ?>

			<?php
			if ( Yii::app()->user->checkAccess('deleteOwnBlog',
				array('ownerId' => $data->ownerId)) || Yii::app()->user->checkAccess('deleteBlog')
			) {
			?>
			<?php
			$this->widget('bootstrap.widgets.TbButton',
				array(
				     'buttonType' => 'link',
				     'type'       => 'danger',
				     'label'      => 'Delete',
				     'url'         => array(
					     '/blogs/default/delete',
					     'id' => $data->getId()
				     ),
				     'htmlOptions' => array(
					     'csrf' => true,
					     'href' => array(
						     '/blogs/default/delete',
						     'id' => $data->getId()
					     ),
				     )
				));
			?>
				&nbsp;
			<?php } ?>
		</div>
    </div>
</div>
<hr />