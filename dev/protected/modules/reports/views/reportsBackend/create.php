<?php
echo '<h1>' . Yii::t('newsModule.common', 'Создание новости') . '</h1>';

$this->renderPartial('_form', array(
                                      'model' => $model,
                                 ));