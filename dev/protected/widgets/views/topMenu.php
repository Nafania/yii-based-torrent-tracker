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
		     $items,
		     '<form class="form-search navbar-search pull-right" action="' . Yii::app()->createUrl('/torrents/default/index/') . '"><div class="input-prepend"><span class="add-on"><i class="icon-search"></i></span>' . CHtml::textField('search',
			     Yii::app()->getRequest()->getParam('search'),
			     array(
			          'class'       => 'input-medium',
			          'placeholder' => Yii::t('common', 'Search')
			     )) . '</div></form>',

	     )
	));?>