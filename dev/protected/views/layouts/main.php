<?php /* @var $this Controller */

$cs = Yii::app()->getClientScript();
$cs->registerPackage('common');
/*if ( !Yii::app()->getUser()->getIsGuest() ) {
    $cs->registerPackage('theme-' . Yii::app()->getUser()->getModel()->profile->theme);
}
else {
    $cs->registerPackage('theme-default');
}*/
$cs->registerPackage('theme-default');
//$cs->registerPackage('BOOTSTRA.386');
if ( date('m') == 12 && date('d') > 25 || date('m') == 1 && date('d') < 15 ) {
	$cs->registerCssFile('/css/new-year.css');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="ru" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<?php $this->widget('application.widgets.TopMenu'); ?>
<?php
$this->widget('application.widgets.YaShare', array());
?>
<?php

	if ( isset($this->breadcrumbs) ):
		$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'encodeLabel' => false,
            'links'=>$this->breadcrumbs,
    ));
		?><!-- breadcrumbs -->
<?php endif ?>

<?php
$this->widget('bootstrap.widgets.TbAlert',
	array(
		'block'     => true,
		'fade'      => true,
		'closeText' => '×',
		'alerts'    => array( // configurations per alert type
		                      'success',
		                      'info',
		                      'warning',
		                      'error',
		                      'danger'
		                      // success, info, warning, error or danger
		),
	));

?>
<?php $this->widget('application.modules.advertisement.widgets.AdsBlockWidget',
	array('systemName' => 'header'))
?>

<section class="container-fluid">
	<div class="row-fluid">
		<?php echo $content; ?>
	</div>


	<?php $this->widget('application.modules.advertisement.widgets.AdsBlockWidget',
		array('systemName' => 'footer'))
	?>

	<div class="row-fluid footer">

     <div class="span4">
         <h4>Стримзон - пришел, увидел и скачал!</h4>
         Что может быть проще, чем качать торренты с нашего трекера? Все торренты бесплатно, без регистрации и смс. Просто найдите нужную вам раздачу через поиск торрентов, кликните на Скачать торрент, запустится ваш любимый торрент-клиент и торрент начнет качаться.
     </div>

     <div class="span4">
         <h4>StreamZone - лучший торрент-трекер рунета</h4>
         Добро пожаловать на торрент-трекер StreamZone (стримзон). Наш торрент-трекер является лучшим торрент-трекером 2011 года по версии сайта uptracker. Это подверждается большим количеством качественных торрентов на нашем сайте. Все торренты проверяются на качество и соотвествие описанию торрента. Для того, чтобы скачать торрент бесплатно не нужны никакие смс. Все торренты бесплатно и без регистрации.
     </div>

     <div class="span4">
         <h4>Streamzone - большой выбор и скорость!</h4>
         На нашем трекере представлено множество качественных торрентов на любой вкус и цвет. Вы также можете сами загружать торренты на наш сайт. Однако администрация этого сайта не несет никакой ответственности за действия пользователей. На сервере хранятся только торрент файлы. Это значит, что мы не храним никаких нелегальных материалов.
     </div>

 </div>
</section>
<?php $this->widget('application.modules.user.widgets.UserMenu'); ?>
<?php $this->widget('application.modules.chat.widgets.MjmChat'); ?>
<?php $this->widget('application.widgets.AnalyticsWidget'); ?>
<?php $this->widget('application.modules.advertisement.widgets.AdsBlockWidget',
	array('systemName' => 'footerCode'))
?>
<?php
echo $this->clips['afterContent'];
?>
</body>
</html>
