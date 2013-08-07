<?php
/*
 * @var $model News
 */
?>
<h1><?php echo Yii::t('newsModule.common', 'Управление новостями'); ?></h1>
<ul class="tools">
    <li>
       <?php echo CHtml::link(Yii::t('newsModule.common', 'Создать новость'),
		    $this->createUrl('/news/newsBackend/create'),
		    array('class' => 'add-handler focus')); ?>
    </li>
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'id'           => 'objects-grid',
	     'dataProvider' => $model->search(),
	     'filter'       => $model,
	     'ajaxUrl'      => Yii::app()->createUrl('/news/newsBackend/index'),
	     'columns'      => array(
		     'id',
		     'title',
		     array(
			     'name'  => 'ctime',
			     'value' => function ( $data ) {
				     return Yii::app()->getDateFormatter()->formatDateTime($data->ctime);
			     }
		     ),
		     'text',
		     array(
			     'class'        => 'DToggleColumn',
			     'name'         => 'pinned',
			     'confirmation' => Yii::t('newsModule.common', 'Изменить статус новости?'),
			     'linkUrl'      => 'news/newsBackend/pin',
			     'filter'       => array(
				     News::PINNED_YES => Yii::t('newsModule.common', 'Pinned'),
				     News::PINNED_NO  => Yii::t('newsModule.common', 'Not pinned'),
			     ),
		     ),
		     array(
			     'name'  => 'owner',
			     'value' => function ( $data ) {
				     return $data->user->getName();
			     }
		     ),
		     array(
			     'class'                => 'YiiAdminButtonColumn',
			     'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
			     'updateButtonUrl'      => "Yii::app()->createUrl('/news/newsBackend/update', array('id' => \$data->getId()))",

			     'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
			     'deleteButtonUrl'      => "Yii::app()->createUrl('/news/newsBackend/delete', array('id' => \$data->getId()))",
			     'viewButtonOptions'    => array('style' => 'display:none;',),
		     ),
	     ),
	));
?>
