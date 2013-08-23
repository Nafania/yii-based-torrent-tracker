<?php $url = Yii::app()->createAbsoluteUrl('user/default/confirmEmail', array('c' => $code)) ?>
<p>Здравствуйте, вы решили подвердить свой email адрес на сайте <?php echo  Yii::app()->config->get('base.siteName') ?>.</p>
<p>Пройдите по ссылке, чтобы подтвердить ваш email адрес:<br />
<a href="<?php echo $url ?>"><?php echo $url ?></a></p>