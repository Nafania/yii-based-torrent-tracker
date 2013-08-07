<h1><?php echo Yii::t('CategoryAttributesModule', 'Управление аттрибутами'); ?></h1>
<ul class="tools">
    <li>
		<?php echo CHtml::link(YiiadminModule::t('Создать'),
		Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/create'),
		array('class' => 'add-handler focus')); ?>

    </li>
</ul>

<?php

$this->widget('zii.widgets.grid.CGridView',
	array(
	     'dataProvider' => $model->search(),
	     'filter'       => $model,
	     'columns'      => array(
		     'title',
		     array(
			     'name'  => 'type',
			     'value' => function ( $data ) {
				     $ary = Attribute::typeLabels();
				     return $ary[$data->type];
			     },
			     'filter' => Attribute::typeLabels()
		     ),
		     array(
			     'name'  => 'validator',
			     'value' => function ( $data ) {
				     return $data->validator;
			     },
		     ),
		     array(
			     'name'  => 'required',
			     'value' => function ( $data ) {
				     return $data->required ? Yii::t('main', 'Да') : Yii::t('main', 'Нет');
			     },
			     'filter' => array(0 => Yii::t('main', 'Нет'), 1 => Yii::t('main', 'Да'))
		     ),
		     array(
			     'name'  => 'description',
			     'value' => function ( $data ) {
				     return $data->description;
			     },
		     ),
		     array(
			     'name'  => 'cId',
			     'value' => function ( $data ) {
				     if ( $data->category ) {
				     return $data->category->getTitle();
				     }
			     },
			     'filter' => CHtml::listData(Category::model()->findAll(), 'id', 'title'),
		     ),
		     array(
			     'class'=> 'CButtonColumn',
			     'template' => '{update} {delete}'
		     )
	     )
	));