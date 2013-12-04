<?php
/*
 * @var $model TorrentGroup
 */
?>
	<h1><?php echo Yii::t('torrentsModule.common', 'Управление торрентами'); ?></h1>
	<ul class="tools">
    <li> 
       <?php echo CHtml::link(Yii::t('torrentsModule.common', 'Создать торрент'),
	       $this->createUrl('/torrents/torrentsBackend/create'),
	       array('class' => 'add-handler focus')); ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'id'            => 'objects-grid',
	     'dataProvider'  => $model->search(),
	     'filter'        => $model,
	     //'ajaxUrl'       => Yii::app()->createUrl('/torrents/torrentsBackend/index'),
	     'enableHistory' => true,
	     'columns'       => array(
		     array(
			     'class'               => 'EImageColumn',
			     'imagePathExpression' => '$data->getImageUrl(40, 60)',
			     'imageOptions'        => array(
				     'style' => 'width:40px;height:60px;'
			     )
		     ),
		     'id',
		     array(
			     'name'  => 'title',
			     'type'  => 'html',
			     'value' => function ( $data ) {
				     echo CHtml::link($data->getTitle(), $data->getUrl(), array('target' => '_blank'));
			     },
		     ),
		     array(
			     'name'   => 'cId',
			     'value'  => function ( $data ) {
				     return $data->category->getTitle();
			     },
			     'filter' => CHtml::listData(Category::model()->findAll(), 'id', 'title'),
		     ),
		     array(
			     'name'  => 'ctime',
			     'value' => function ( $data ) {
				     return Yii::app()->getDateFormatter()->formatDateTime($data->ctime);
			     },
		     ),
		     array(
			     'name'  => 'mtime',
			     'value' => function ( $data ) {
				     return Yii::app()->getDateFormatter()->formatDateTime($data->mtime);
			     },
		     ),
		     array(
			     'class'                => 'YiiAdminButtonColumn',
			     'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
			     'updateButtonUrl'      => "Yii::app()->createUrl('/torrents/torrentsBackend/update', array('id' => \$data->getId()))",

			     'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
			     'deleteButtonUrl'      => "Yii::app()->createUrl('/torrents/torrentsBackend/delete', array('id' => \$data->getId()))",
			     'viewButtonOptions'    => array('style' => 'display:none;',),
		     ),
	     ),
	));
?>
<?php
$this->widget('application.modules.yiiadmin.widgets.manageSelected.manageSelected',
	array(
	     'gridId' => 'objects-grid',
	     'buttons' => array(
		     array(
			     'url'          => Yii::app()->createUrl('/torrents/torrentsBackend/merge'),
			     'title'        => Yii::t('torrentsModule.common', 'Объединить'),
			     'class'        => 'merge',
			     'confirmation' => Yii::t('torrentsModule.common',
				     "Вы уверены, что хотите объединить выбранные торренты?\nК первому выбранному торренту будут присоединены последующие.")
		     )
	     ),
	));
?>