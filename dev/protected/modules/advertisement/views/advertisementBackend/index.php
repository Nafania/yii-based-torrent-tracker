<?php
/*
 * @var $model StaticPage
 */
?>
	<h1><?php echo Yii::t('advertisementModule.common', 'Управление рекламой'); ?></h1>
	<ul class="tools">
    <li> 
       <?php echo CHtml::link(Yii::t('advertisementModule.common', 'Создать рекламный блок'),
		    $this->createUrl('/advertisement/advertisementBackend/create'),
		    array('class' => 'add-handler focus')); ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'id'           => 'objects-grid',
	     'dataProvider' => $model->search(),
	     'filter'       => $model,
	     'ajaxUrl'      => Yii::app()->createUrl('/advertisement/advertisementBackend/index'),
	     'columns'      => array(
		     'id',
		     'systemName',
		     'description',
		     array(
			     'class'                => 'YiiAdminButtonColumn',
			     'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
			     'updateButtonUrl'      => "Yii::app()->createUrl('/advertisement/advertisementBackend/update', array('id' => \$data->getId()))",

			     'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
			     'deleteButtonUrl'      => "Yii::app()->createUrl('/advertisement/advertisementBackend/delete', array('id' => \$data->getId()))",
			     'viewButtonOptions'    => array('style' => 'display:none;',),
		     ),
	     ),
	));
?>