<?php
/* @var $this ProblemsController */
/* @var $problem Problem */
?>

	<h1>Update Blog <?php echo $blog->getTitle(); ?></h1>

<?php echo $this->renderPartial('_form',
	array(
	     'blog' => $blog,
	)); ?>