<?php
/* @var $this DefaultController */
/* @var $blog Blog */
?>

	<h1><?php echo Yii::t('blogsModule.common',
			'Редактирование блога {blogTitle}',
			array(
			     '{blogTitle}' => $blog->getTitle()
			)); ?></h1>

<?php echo $this->renderPartial('_form',
	array(
	     'blog' => $blog,
	)); ?>