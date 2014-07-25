<?php
/* @var $this DefaultController */
/* @var $model modules\blogs\models\Blog */
?>
<div class="media">
    <?php
    if ( $model->user ) {
        $img = $model->user->profile->getImageUrl(100, 100);
        $alt = $model->user->getName();
        $url = $model->user->getUrl();
    }
    else {
        $img = '/images/no_photo.png';
        $alt = '';
        $url = '';
    }
    echo CHtml::link(CHtml::image($img,
            $alt,
            [
                'class' => 'media-object img-polaroid',
                'style' => 'width:100px;height:100px;',
            ]),
        $url,
        ['class' => 'pull-left']);
    ?>

	<div class="media-body">
        <h1 class="media-heading"><?php echo $model->getTitle(); ?></h1>
        <p><?php echo $model->getDescription(); ?></p>

		<p class="pull-right">
            <?php
            $this->widget('application.modules.subscriptions.widgets.SubscriptionButton', array(
                'model' => $model,
            ));
            ?>

            <?php
          		if ( Yii::app()->user->checkAccess('createPostInOwnBlog',
          			array('ownerId' => $model->ownerId)) || Yii::app()->user->checkAccess('createPostInBlog')
          		):
          		?>

			<?php $this->widget('bootstrap.widgets.TbButton',
				array(
				     'buttonType'  => 'link',
				     'type'        => 'primary',
				     'label'       => Yii::t('blogsModule.common', 'Написать пост'),
				     'url'         => array(
					     '/blogs/post/create', 'blogId' => $model->getId()
				     ),
				));
			?>
            <?php endif; ?>

        </p>

    </div>
</div>
<hr />

<?php $this->widget('bootstrap.widgets.TbListView',
	array(
	     'id'              => 'blogPostsListView',
	     'dataProvider'    => $postsProvider,
	     'itemView'        => 'application.modules.blogs.views.post._view',
	     'template'        => '{sorter} {items} {pager}',
	     'sortableAttributes' => array(
		     'ctime',
		     'commentsCount',
		     'rating',
             'lastCommentCtime'
	     ),
	));
?>