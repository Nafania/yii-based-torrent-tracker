<?php
/* @var $this DefaultController */
/* @var $blog Blog */
?>

<h1><?php echo Yii::t('blogsModule.common', 'Создание блога') ?></h1>

<?php echo $this->renderPartial('_form',
	array(
	     'blog' => $blog,
	)); ?>