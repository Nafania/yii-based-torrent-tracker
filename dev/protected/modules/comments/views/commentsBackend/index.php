<?php
/*
 * @var $model Comment
 */
?>
	<h1><?php echo Yii::t('commentsModule.common', 'Управление комментариями'); ?></h1>
	<ul class="tools">
    <li> 
       <?php /*echo CHtml::link(Yii::t('commentsModule.common', 'Создать проблему'),
		    $this->createUrl('/problems/problemsBackend/create'),
		    array('class' => 'add-handler focus'));*/
       ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
		'id'           => 'objects-grid',
		'dataProvider' => $dataProvider,
		'filter'       => $model,
		'ajaxUrl'      => Yii::app()->createUrl('/comments/commentsBackend/index'),
		'columns'      => array(
			'id',
			'text',
			array(
				'name'  => 'ctime',
				'value' => '$data->getCtime(true)',
			),
			'modelName',
			'modelId',
			array(
				'name'  => 'user',
				'value' => function ( $data ) {
						if ( $data->user ) {
							echo $data->user->getName();
						}
						else {
							echo '---';
						}
					},
			),
			array(
				'name'   => 'status',
				'value'  => '$data->getStatusLabel()',
				'filter' => $model->statusLabels(),
			),
			array(
				'class'                => 'YiiAdminButtonColumn',
				'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
				'updateButtonUrl'      => "Yii::app()->createUrl('/comments/commentsBackend/update', array('id' => \$data->getId()))",

				'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
				'deleteButtonUrl'      => "Yii::app()->createUrl('/comments/commentsBackend/delete', array('id' => \$data->getId()))",
				'viewButtonOptions'    => array('style' => 'display:none;',),
			),
		),
	));
?>