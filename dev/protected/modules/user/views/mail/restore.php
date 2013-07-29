<?php $hash = Yii::app()->createAbsoluteUrl('user/default/reset', array('hash' => $model->resetHash)) ?>
<p>Здравсвуйте, вы запросили восстановление пароля на сайте <?php echo  Yii::app()->config->get('base.siteName') ?>.</p>
<p>Для смены вашего пароля пройдите по ссылке <a href="<?php echo $hash ?>"><?php echo $hash ?></a></p>