<?php
/* @var $this ProblemsController */
/* @var $model Problem */
?>

	<h1>Create Blog</h1>

<?php echo $this->renderPartial('_form',
	array(
	     'blog' => $blog,
	)); ?>