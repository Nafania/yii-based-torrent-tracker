<?php
/*
 * @var CArrayDataProvider $dataProvider
 */
?>
	<h1><?= $this->pageTitle ?></h1>
	<ul class="tools">
    <li> 
       <?php echo CHtml::link(Yii::t('autotagsModule.common', 'Создать тег'),
		    $this->createUrl('/autotags/autotagsBackend/create'),
		    ['class' => 'add-handler focus']); ?>
    </li> 
    </ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'id'           => 'objects-grid',
	     'dataProvider' => $dataProvider,
	     'ajaxUrl'      => Yii::app()->createUrl('/autotags/autotagsBackend/index'),
	     'columns'      => [
		     'tag_name',
		     'cat_name',
		     [
			     'class'                => 'YiiAdminButtonColumn',
			     'deleteButtonUrl'      => function($data) {
					 return Yii::app()->createUrl('/autotags/autotagsBackend/delete', ['id' => $data['id_auto_tag']]);
					},
			     'viewButtonOptions'    => ['style' => 'display:none;',],
				 'updateButtonOptions'    => ['style' => 'display:none;',],
		     ],
	     ],
	));
?>