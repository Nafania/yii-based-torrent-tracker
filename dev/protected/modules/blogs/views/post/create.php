<?php
/* @var $this PostController */
/* @var $blogPost BlogPOst */
?>

	<h1>Create Post</h1>

<?php echo $this->renderPartial('_form',
	array(
	     'blogPost' => $blogPost,
	     'blog' => $blog
	)); ?>