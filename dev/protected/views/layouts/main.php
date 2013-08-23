<?php /* @var $this Controller */

$cs = Yii::app()->getClientScript();
$cs->registerPackage('common');
$cs->registerCssFile('/css/style.css');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="ru" />
	<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<?php $this->widget('application.widgets.TopMenu'); ?>

<?php

	if ( isset($this->breadcrumbs) ):
		$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'encodeLabel' => false,
            'links'=>$this->breadcrumbs,
    ));
		?><!-- breadcrumbs -->
<?php endif?>

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
<section class="container-fluid">
	<div class="row-fluid">
		<?php echo $content; ?>
	</div>
</section>
<?php $this->widget('application.modules.user.widgets.UserMenu'); ?>
</body>
</html>
