<?php
echo '<h1>' . Yii::t('CategoryModule.common', 'Создание категории') . '</h1>';

$this->renderPartial('_addForm', array(
                                      'model' => $model,
                                      'action' => $action
                                 ));