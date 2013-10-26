<?php
/* @var $this PostController */
/* @var $blogPost BlogPost */
/* @var $blog Blog */
?>

	<h1><?php echo Yii::t('blogsModule.common',
			'Редактирование поста в блоге {blogTitle}',
			array('{blogTitle}' => $blog->getTitle())) ?></h1>

<?php echo $this->renderPartial('_form',
	array(
	     'blogPost' => $blogPost,
	     'blog'     => $blog,
	)); ?>