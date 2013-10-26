<?php
/**
 * @var $data Blog
 */
?>
<div class="media blogsList">
	<?php
	$img = CHtml::image($data->user->profile->getImageUrl(80, 80),
		$data->user->getName(),
		array(
		     'class' => 'media-object img-polaroid',
		     'style' => 'width:80px'
		));
	echo CHtml::link($img, $data->user->getUrl(), array('class' => 'pull-left'));
	?>

	<div class="media-body">
        <h3 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h3>

        <p><?php echo StringHelper::cutStr($data->getDescription()); ?></p>

		<div class="pull-right">
			<?php
			if ( Yii::app()->user->checkAccess('createPostInGroupMemberBlog',
					array('isMember' => Group::checkJoin($data->group))) || Yii::app()->user->checkAccess('createPostInOwnBlog',
					array('ownerId' => $data->ownerId)) || Yii::app()->user->checkAccess('createPostInBlog')
			) {
				?>
				<?php $this->widget('bootstrap.widgets.TbButton',
					array(
					     'buttonType'  => 'link',
					     'icon'        => 'pencil',
					     'url'         => array(
						     '/blogs/post/create',
						     'blogId' => $data->getId()
					     ),
					     'htmlOptions' => array(
						     'data-toggle'         => 'tooltip',
						     'data-placement'      => 'top',
						     'data-original-title' => Yii::t('blogsModule.common', 'Написать пост'),
					     )
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
					     'type'        => 'success',
					     'icon'        => 'edit',
					     'url'         => array(
						     '/blogs/default/update',
						     'id' => $data->getId()
					     ),
					     'htmlOptions' => array(
						     'csrf'                => true,
						     'href'                => array(
							     '/blogs/default/update',
							     'id' => $data->getId()
						     ),
						     'data-toggle'         => 'tooltip',
						     'data-placement'      => 'top',
						     'data-original-title' => Yii::t('blogsModule.common', 'Редактировать блог'),
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
					     'buttonType'  => 'link',
					     'type'        => 'danger',
					     'icon'        => 'trash',
					     'url'         => array(
						     '/blogs/default/delete',
						     'id' => $data->getId()
					     ),
					     'htmlOptions' => array(
						     'csrf'                => true,
						     'href'                => array(
							     '/blogs/default/delete',
							     'id' => $data->getId()
						     ),
						     'data-toggle'         => 'tooltip',
						     'data-placement'      => 'top',
						     'data-original-title' => Yii::t('blogsModule.common', 'Удалить блог'),
					     )
					));
				?>
				&nbsp;
			<?php } ?>
		</div>
    </div>
</div>
<hr />