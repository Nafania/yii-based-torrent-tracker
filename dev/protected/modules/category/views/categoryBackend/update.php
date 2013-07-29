<?php
echo '<h1>' . Yii::t('CategoryModule', 'Редактирование категории') . '</h1>';

$this->renderPartial('_addForm', array(
                                      'model' => $model,
                                      'action' => $action
                                 ));