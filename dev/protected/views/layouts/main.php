<?php /* @var $this Controller */
Yii::app()->getClientScript()->registerPackage('common');
Yii::app()->getClientScript()->registerCssFile('/css/style.css');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

		<?php $this->widget('bootstrap.widgets.TbNavbar',
			array(
			     'type'     => null,
			     'fixed'    => false,
			     // null or 'inverse'
			     'brand'    => false,
			     //'brandUrl' => '#',
			     'collapse' => true,
			     // requires bootstrap-responsive.css
			     'items'    => array(
				     array(
					     'class'       => 'bootstrap.widgets.TbMenu',
					     'encodeLabel' => false,
					     'items'       => array(
						     array(
							     'label'   => '<img style="width:18px;height: 18px;" src="http://placehold.it/18x18">
							     <span class="badge badge-info">1</span>',
							     'url'     => '#',
							     'visible' => !Yii::app()->getUser()->getIsGuest(),
							     'items'   => array(
								     array(
									     'label' => 'Друзья',
									     'url'   => '#',
								     ),
								     array(
									     'label' => 'Профиль',
									     'url'   => '#'
								     ),
								     array(
									     'label' => 'Настройки',
									     'url'   => '#'
								     ),
								     array(
									     'label' => 'Закладки',
									     'url'   => '#'
								     ),
								     array(
									     'label' => 'Выход',
									     'url'   => array('/user/default/logout'),
								     ),

							     ),
						     ),
						     array(
							     'label'   => 'Лента  <span class="badge badge-success">2</span> ',
							     'url'     => '#',
							     'visible' => !Yii::app()->getUser()->getIsGuest(),
							     'items'   => array(
								     array(
									     'label' => '<i class="icon-envelope"></i> Новое сообщение',
									     'url'   => '#',
								     ),
								     array(
									     'label' => '<i class="icon-download-alt"></i> Добавлен новый торрент',
									     'url'   => '#'
								     ),
								     array(
									     'label' => '<i class="icon-user"></i> Вас добавили в друзья',
									     'url'   => '#'
								     ),
								     array(
									     'label' => '<i class="icon-tag"></i> Вы получили новый значок',
									     'url'   => '#'
								     ),

							     ),
						     ),
						     '---',
						     array(
							     'label' => 'Home',
							     'url'   => '/',
						     ),
						     array(
							     'label' => 'Torrents',
							     'url'   => array('/torrents/default/index'),
						     ),
						     array(
							     'label' => 'Upload',
							     'url'   => array('/torrents/default/create'),
						     ),
						     array(
							     'label' => 'Rules',
							     'url'   => '#'
						     ),
						     array(
							     'label' => 'FAQ',
							     'url'   => '#'
						     ),
						     array(
							     'label'       => Yii::t('userModule.common', 'Login'),
							     'url'         => array('/user/default/login'),
							     'linkOptions' => array(
								     'data-toggle' => 'modal',
								     'data-target' => '#loginModal',
							     ),
							     'visible' => Yii::app()->getUser()->getIsGuest(),
						     ),
						     array(
							     'label'       => Yii::t('userModule.common', 'Register'),
							     'url'         => array('/user/default/register'),
							     'linkOptions' => array(
								     'data-toggle' => 'modal',
								     'data-target' => '#registerModal',
							     ),
							     'visible' => Yii::app()->getUser()->getIsGuest(),
						     ),
					     ),
				     ),
				     '<form class="navbar-search pull-right" action="' . Yii::app()->createUrl('/torrents/default/index/') . '">' . CHtml::textField('search',
					     Yii::app()->getRequest()->getParam('search'),
					     array(
					          'class'       => 'search-query span2',
					          'placeholder' => Yii::t('common', 'Search')
					     )) . '</form>',

			     ),
			));?>
		</div><!-- mainmenu -->
	<?php

	if ( isset($this->breadcrumbs) ):
		$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
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
