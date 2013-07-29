<?php
/*
 * @var $model StaticPage
 */
?>
<h1><?php echo Yii::t('staticpagesModule.common', 'Управление статичными страницами'); ?></h1>
<ul class="tools">
    <li> 
       <?php echo CHtml::link(Yii::t('staticpagesModule.common', 'Создать страницу'),
		    $this->createUrl('/staticpages/staticpagesBackend/create'),
		    array('class' => 'add-handler focus')); ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'id'           => 'objects-grid',
	     'dataProvider' => $model->search(),
	     'filter'       => $model,
	     'ajaxUrl'      => Yii::app()->createUrl('/staticpages/staticpagesBackend/index'),
	     'columns'      => array(
		     'id',
		     'title',
		     'pageTitle',
		     'url',
		     'published',
		     array(
			     'class'                => 'YiiAdminButtonColumn',
			     'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
			     'updateButtonUrl'      => "Yii::app()->createUrl('/staticpages/staticpagesBackend/update', array('id' => \$data->getId()))",

			     'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
			     'deleteButtonUrl'      => "Yii::app()->createUrl('/staticpages/staticpagesBackend/delete', array('id' => \$data->getId()))",
			     'viewButtonOptions'    => array('style' => 'display:none;',),
		     ),
	     ),
	));
?>