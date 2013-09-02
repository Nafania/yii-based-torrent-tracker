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
		//TODO move events generate to events module
		$events = Event::model()->unreaded()->forCurrentUser()->findAll();

		$eventItems = array();

		foreach ( $events AS $event ) {
			$icon = $event->getIcon();
			$eventItems[] = array(
				'label'       => '<i class="icon-' . $icon . '"></i> ' . CHtml::encode($event->getText()),
				'url'         => $event->getUrl(),
				'linkOptions' => array(
					'data-action'   => 'event',
					'data-id'       => $event->getId(),
					'data-eventurl' => Yii::app()->createUrl('/subscriptions/event/read')
				)
			);
		}
		$sizeOfEvents = sizeof($events);

		$items = array(
			'class'       => 'bootstrap.widgets.TbMenu',
			'encodeLabel' => false,
			'items'       => array(
				array(
					'label' => 'Home',
					'url'   => array('/site/index'),
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
					'label' => 'Blogs',
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
			$rating = Yii::app()->getUser()->getModel()->rating;
			if ( $rating ) {
				$rating = $rating->getRating();
			}
			else {
				$rating = 0;
			}

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
				                                        'label'   => 'Лента' . ( $sizeOfEvents ? ' <span class="badge badge-success">' . $sizeOfEvents . '</span> ' : '' ),
				                                        'url'     => ( $sizeOfEvents ? '#' : '' ),
				                                        'visible' => !Yii::app()->getUser()->getIsGuest(),
				                                        'items'   => $eventItems

			                                        ),
			                                        '---'
			                                   ),
				$items['items']);
		}

		return $items;
	}
}