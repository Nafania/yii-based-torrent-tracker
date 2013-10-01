<?php
class TopMenu extends CWidget {
	public function run () {
		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('subscriptions')->getAssetsUrl() . '/js/events.js');

		$this->render('topMenu',
			array(
			     'items' => $this->_getItems()
			));
	}

	private function _getItems () {
		//$eventItems = Yii::app()->getModule('subscriptions')->getEventsMenu();
		$items = array(
			'class'       => 'bootstrap.widgets.TbMenu',
			'encodeLabel' => false,
			'items'       => array(
				array(
					'label' => Yii::t('common', 'Home'),
					'url'   => array('/site/index'),
				),
				array(
					'label' => Yii::t('torrentsModule.common', 'Torrents'),
					'url'   => array('/torrents/default/index'),
				),
				array(
					'label' => Yii::t('torrentsModule.common', 'Upload'),
					'url'   => array('/torrents/default/create'),
				),
				array(
					'label' => Yii::t('blogsModule.common', 'Blogs'),
					'url'   => array('/blogs/default/index'),
				),
			),
		);

		$items['items'] = CMap::mergeArray($items['items'],
			Yii::app()->getModule('staticpages')->getPublishedPagesAsMenu());

		$items['items'] = CMap::mergeArray($items['items'],
			array(
			     array(
				     'label'       => Yii::t('userModule.common', 'Login'),
				     'url'         => array('/user/default/login'),
				     'linkOptions' => array(
					     'data-toggle' => 'modal',
					     'data-target' => '#loginModal',
				     ),
				     'visible'     => Yii::app()->getUser()->getIsGuest(),
			     ),
			     array(
				     'label'       => Yii::t('userModule.common', 'Register'),
				     'url'         => array('/user/default/register'),
				     'linkOptions' => array(
					     'data-toggle' => 'modal',
					     'data-target' => '#registerModal',
				     ),
				     'visible'     => Yii::app()->getUser()->getIsGuest(),
			     ),
			));

		if ( !Yii::app()->getUser()->getIsGuest() ) {
			$rating = (int) Yii::app()->getUser()->getModel()->getRating();

			if ( $rating > 0 ) {
				$class = 'badge-success';
			}
			elseif ( $rating < 0 ) {
				$class = 'badge-important';
			}
			else {
				$class = 'badge-info';
			}

			$items['items'] = CMap::mergeArray(array(
			                                        array(
				                                        'label'   => CHtml::image(Yii::app()->getUser()->profile->getImageUrl(18,
						                                        18),
					                                        Yii::app()->getUser()->getName(),
					                                        array(
					                                             'width'  => '18',
					                                             'height' => '18'
					                                        )) . ' <span class="badge ' . $class . '">' . $rating . '</span>',
				                                        'url'     => '#',
				                                        'visible' => !Yii::app()->getUser()->getIsGuest(),
				                                        'items'   => array(
					                                        array(
						                                        'label' => 'Друзья',
						                                        'url'   => '#',
					                                        ),
					                                        array(
						                                        'label' => 'Профиль',
						                                        'url'   => array(
							                                        '/user/default/view',
							                                        'id' => Yii::app()->getUser()->getId()
						                                        ),
					                                        ),
					                                        array(
						                                        'label' => 'Мои блоги',
						                                        'url'   => array('/blogs/default/my'),
					                                        ),
					                                        array(
						                                        'label' => 'Настройки',
						                                        'url'   => array('/user/default/settings'),
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
				                                        'label'       => 'Лента ' . $this->widget('application.modules.subscriptions.widgets.EventsWidget',
					                                        array(),
					                                        true),
				                                        'url'         => array('/subscriptions/event/getList'),
				                                        'visible'     => !Yii::app()->getUser()->getIsGuest(),
				                                        'linkOptions' => array(
					                                        'id' => 'eventsMenu',
					                                        'class' => 'dropdown-toggle',
					                                        'data-toggle' => 'dropdown'
				                                        ),
				                                        'itemOptions' => array(
					                                        'class' => 'dropdown'
				                                        )

				                                        //'items'   => $eventItems
			                                        ),
			                                        '---'
			                                   ),
				$items['items']);
		}

		return $items;
	}
}