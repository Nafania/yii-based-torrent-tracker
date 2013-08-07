<?php /* @var $this Controller */
Yii::app()->getClientScript()->registerPackage('common');
Yii::app()->getClientScript()->registerCssFile('/css/style.css');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

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
