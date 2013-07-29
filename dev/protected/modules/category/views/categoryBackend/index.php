<?php

echo '<h1>' . Yii::t('CategoryModule', 'Создание категории') . '</h1>';

$this->renderPartial('_addForm', array(
                                      'model' => $model,
                                      'action' => $action
                                 ));

$this->widget('application.modules.category.extensions.NestedDynaTree.NestedDynaTree',
	array(
	     //the class name of the model.
	     'modelClass'             => "Category",
	     // action taken on click on item. (default empty)
	     //'clickAction'            => 'category/categoryBackend/update',
	     //if given, AJAX load a result of clickAction to the container (default empty)
	     'clickAjaxLoadContainer' => 'content',
	     //can insert, delete and ( if enabled)drag&drop (default true)
	     //'manipulationEnabled' => !Yii::app()->user->isGuest,
	     //can sort items by drag&drop (default true)
	     'dndEnabled'             => true,

	     //AJAX controller absolute path if you don`t use controllerMap
	     'ajaxController'         => 'category/categoryBackend',
	     'skin' => 'vista',
	     'htmlOptions'            => array(
		     'id' => 'categoriesTree'
	     ),
	     'options' => array(
		     'onDblClick'=> 'js:function(event) {var node = $("#categoriesTree").dynatree("getActiveNode");window.location.href="' . Yii::app()->createUrl('category/categoryBackend/update') . '/id/" + node.data.key}',
	     ),

	));