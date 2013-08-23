<?php
/*
 * @var $model StaticPage
 */
?>
	<h1><?php echo Yii::t('blogsModule.common', 'Управление постами'); ?></h1>
	<ul class="tools">
    <li> 
       <?php echo CHtml::link(Yii::t('blogsModule.common', 'Создать пост'),
		    $this->createUrl('/blogs/blogsBackend/create'),
		    array('class' => 'add-handler focus')); ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'id'           => 'objects-grid',
	     'dataProvider' => $model->search(),
	     'filter'       => $model,
	     'ajaxUrl'      => Yii::app()->createUrl('/blogs/blogsBackend/index'),
	     'columns'      => array(
		     'id',
		     'title',
		     array(
			     'class'        => 'DToggleColumn',
			     'name'         => 'hided',
			     'confirmation' => Yii::t('blogsModule.common', 'Изменить статус?'),
			     'linkUrl' => Yii::app()->createUrl('blogs/blogsBackend/toggle'),
			     'filter'       => array(
				     BlogPost::HIDED     => Yii::t('blogsModule.common', 'Hided'),
				     BlogPost::NOT_HIDED => Yii::t('blogsModule.common', 'Not hided')
			     ),
		     ),
		     'ctime',
		     array(
			     'class'                => 'YiiAdminButtonColumn',
			     'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
			     'updateButtonUrl'      => "Yii::app()->createUrl('/blogs/blogsBackend/update', array('id' => \$data->getId()))",

			     'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
			     'deleteButtonUrl'      => "Yii::app()->createUrl('/blogs/blogsBackend/delete', array('id' => \$data->getId()))",
			     'viewButtonOptions'    => array('style' => 'display:none;',),
		     ),
	     ),
	));
?>