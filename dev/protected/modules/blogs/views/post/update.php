<?php
/* @var $this PostController */
/* @var $blogPost BlogPost */
?>

	<h1>Update Post <?php echo $blogPost->getTitle(); ?></h1>

<?php echo $this->renderPartial('_form',
	array(
	     'blogPost' => $blogPost,
	     'blog'     => $blog,
	)); ?>