<?php
/*
 * @var $model Comment
 */
?>
	<h1><?php echo Yii::t('reportsModule.common', 'Управление жалобами'); ?></h1>
	<ul class="tools">
    <li> 
       <?php /*echo CHtml::link(Yii::t('reportModule.common', 'Создать проблему'),
		    $this->createUrl('/problems/problemsBackend/create'),
		    array('class' => 'add-handler focus'));*/
       ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'id'           => 'objects-grid',
	     'dataProvider' => $model->search(),
	     'filter'       => $model,
	     'ajaxUrl'      => Yii::app()->createUrl('/reports/reportsBackend/index'),
	     'columns'      => array(
		     'modelName',
		     'modelId',
		     array(
			     'name'   => 'state',
			     'value'  => '$data->getStateLabel()',
			     'filter' => $model->stateLabels(),
		     ),
		     array(
			     'header' => 'reports',
			     'value'  => function ( $data ) {
				     foreach ( $data->contents AS $content ) {
					     echo $content->getCtime(true) . ' ' . $content->user->getName() . ' ' . $content->getText() . "<br>";
				     }
			     }
		     ),
		     array(
			     'header' => 'link',
			     'value'  => function ( $data ) {
				     return CHtml::link('Link', $data->getUrl(), array('target' => '_blank'));
			     },
			     'type' => 'raw',
		     ),
		     array(
			     'class'                => 'YiiAdminButtonColumn',
			     'updateButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_changelink.gif',
			     'updateButtonUrl'      => "Yii::app()->createUrl('/reports/reportsBackend/update', array('id' => \$data->getId()))",
			     'updateButtonOptions'  => array('style' => 'display:none;',),

			     'deleteButtonImageUrl' => Yii::app()->getModule('yiiadmin')->getAssetsUrl() . '/img/admin/icon_deletelink.gif',
			     'deleteButtonUrl'      => "Yii::app()->createUrl('/reports/reportsBackend/delete', array('id' => \$data->getId()))",
			     'viewButtonOptions'    => array('style' => 'display:none;',),
		     ),
	     ),
	));
?>