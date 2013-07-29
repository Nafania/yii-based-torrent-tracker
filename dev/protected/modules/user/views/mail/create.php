<?php $url = Yii::app()->createAbsoluteUrl('user/default/login') ?>
<p>Здравствуйте, вы зарегистрировались на сайте <?php echo  Yii::app()->config->get('base.siteName') ?>.</p>
<p>Данные для входа на сайт:<br />
	email <?php echo $model->email; ?><br />
	пароль <?php echo $model->password; ?><br />
	адрес для входа <a href="<?php echo $url ?>"><?php echo $url ?></a></p>