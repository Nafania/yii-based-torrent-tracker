<?php
/**
 * @var $blogPost BlogPost
 */
?>

	<div class="media">

    <div class="media-body">
	    <p class="pull-right">
	    <?php
		    if ( Yii::app()->user->checkAccess('updatePostInOwnBlog',
			    array('ownerId' => $blogPost->ownerId)) || Yii::app()->user->checkAccess('updatePostInBlog')
		    ) {
			    ?>

			    <?php $this->widget('bootstrap.widgets.TbButton',
				    array(
				         'buttonType' => 'link',
				         'type'       => 'primary',
				         'label'      => Yii::t('blogsModule.common', 'Редактировать'),
				         'url'        => array(
					         '/blogs/post/update',
					         'id' => $blogPost->getId()
				         ),
				    ));
			    ?>
		    <?php } ?>
		    <?php
		    if ( Yii::app()->user->checkAccess('deletePostInOwnBlog',
			    array('ownerId' => $blogPost->ownerId)) || Yii::app()->user->checkAccess('deletePostInBlog')
		    ) {
			    ?>
			    <?php $this->widget('bootstrap.widgets.TbButton',
				    array(
				         'buttonType'  => 'submitLink',
				         'type'        => 'danger',
				         'label'       => Yii::t('blogsModule.common', 'Удалить'),
				         'url'         => array(
					         '/blogs/post/delete',
					         'id' => $blogPost->getId()
				         ),
				         'htmlOptions' => array(
					         'csrf' => true,
					         'href' => array(
						         '/blogs/post/delete',
						         'id' => $blogPost->getId()
					         ),
				         )
				    ));
			    ?>
		    <?php } ?>
	    </p>
	    <h2 class="media-heading"><?php echo CHtml::link($blogPost->getTitle(), $blogPost->getUrl()) ?></h2>
	    <?php echo $blogPost->getText(); ?>
	    <hr />


	    <p>
		    <abbr title="<?php echo Yii::t('blogsModule.common',
			    'Добавлено: {date}',
			    array(
			         '{date}' => $blogPost->getCtime('d.m.Y H:i')
			    )) ?>"><?php echo TimeHelper::timeAgoInWords($blogPost->ctime); ?></abbr>
		    |
		    <?php echo CHtml::link($blogPost->user->getName(), $blogPost->user->getUrl()); ?>
		    <?php
		    if ( $tags = $blogPost->getTags() ) {
			    $tagsStr = '';
			    foreach ( $tags AS $tag ) {
				    $tagsStr .= ($tagsStr ? ', ' : '') . '<strong>' . CHtml::link($tag,
					    array(
					         '/blogs/default/view',
					         'id'   => $blogPost->blog->getId(),
					         'tags' => $tag
					    )) . '</strong>';
			    }
			    echo ' | ' . $tagsStr;
		    }
		    ?>
     </p>

	</div>
</div>
	<hr />
<?php $this->widget('application.modules.comments.widgets.CommentsTreeWidget',
	array(
	     'model' => $blogPost,
	)); ?>

<?php $this->widget('application.modules.comments.widgets.AnswerWidget',
	array(
	     'model'    => $blogPost,
	     'torrents' => $model->torrents
	)); ?>