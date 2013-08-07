<?php
/* @var $this NewsBackendController */
/* @var $model News */

echo '<h1>' . Yii::t('newsModule.common', 'Редактирование новости') . '</h1>';

echo $this->renderPartial('_form', array('model'=>$model));