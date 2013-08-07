<?php $this->widget('bootstrap.widgets.TbNavbar',
	array(
	     'type'     => null,
	     'fixed'    => false,
	     // null or 'inverse'
	     'brand'    => false,
	     //'brandUrl' => '#',
	     'collapse' => true,
	     // requires bootstrap-responsive.css
	     'items'    => array($items,
		     '<form class="navbar-search pull-right" action="' . Yii::app()->createUrl('/torrents/default/index/') . '">' . CHtml::textField('search',
			     Yii::app()->getRequest()->getParam('search'),
			     array(
			          'class'       => 'search-query span2',
			          'placeholder' => Yii::t('common', 'Search')
			     )) . '</form>',

	)));?>