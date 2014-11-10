<h1><?php echo Yii::t('torrentsModule.common', 'Загрузка торрента'); ?></h1>

<?php echo $this->renderPartial('_form',
	array(
	     'model' => $model,
	     'category' => $category
	)); ?>