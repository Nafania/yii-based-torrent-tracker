<?php
class TopMenu extends CWidget {
	public function run () {
		$categories = Category::model()->findAll();
		$searchData = Yii::app()->getUser()->getSavedSearchData('TorrentGroup');

		$selectedCategories = Yii::app()->getRequest()->getParam('category', $searchData['category']);
		$selectedTags = Yii::app()->getRequest()->getParam('tags', $searchData['tags']);
		$notTags = Yii::app()->getRequest()->getParam('notTags', $searchData['notTags']);
		$searchVal = Yii::app()->getRequest()->getParam('search', $searchData['search']);
		$sortVal = Yii::app()->getRequest()->getParam('sort', $searchData['sort']);
		$periodVal = Yii::app()->getRequest()->getParam('period', $searchData['period']);

		$this->render('topMenu',
			array(
			     'items'              => $this->_getItems(),
			     'categories'         => $categories,
			     'selectedCategories' => $selectedCategories,
			     'selectedTags'       => $selectedTags,
			     'notTags'            => $notTags,
			     'searchVal'          => $searchVal,
			     'sortVal'            => $sortVal,
			     'periodVal'          => $periodVal,
			     'settingActive'      => $selectedCategories || $selectedTags || $notTags || $searchVal || $sortVal || $periodVal,
			));
	}

	private function _getItems () {
		//$eventItems = Yii::app()->getModule('subscriptions')->getEventsMenu();
		$items = array(
			'class'       => 'bootstrap.widgets.TbMenu',
			'encodeLabel' => false,
			'items'       => array(
				/*array(
					'label' => Yii::t('common', 'Главная'),
					'url'   => array('/site/index'),
				),*/
				array(
					'label' => Yii::t('torrentsModule.common', 'Торренты'),
					'url'   => array('/torrents/default/index'),
				),
				array(
					'label' => Yii::t('torrentsModule.common', 'Загрузить'),
					'url'   => array('/torrents/default/create'),
				),
				array(
					'label' => Yii::t('blogsModule.common', 'Блоги'),
					'url'   => array('/blogs/default/index'),
				),
				array(
					'label' => Yii::t('groupsModule.common', 'Группы'),
					'url'   => array('/groups/default/index'),
				),
			),
		);

		$items['items'] = CMap::mergeArray($items['items'],
			Yii::app()->getModule('staticpages')->getPublishedPagesAsMenu());

		$items['items'] = CMap::mergeArray($items['items'],
			array(
			     array(
				     'label'       => Yii::t('userModule.common', 'Вход'),
				     'url'         => array('/user/default/login'),
				     'linkOptions' => array(
					     'data-toggle' => 'modal',
					     'data-target' => '#loginModal',
				     ),
				     'visible'     => Yii::app()->getUser()->getIsGuest(),
			     ),
			     array(
				     'label'       => Yii::t('userModule.common', 'Регистрация'),
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
				                                        'divider' => true,
			                                        ),
			                                        array(
				                                        'label'   => CHtml::image(Yii::app()->getUser()->profile->getImageUrl(18,
						                                        18),
					                                        Yii::app()->getUser()->getName(),
					                                        array(
					                                             'style' => 'width:18px;height:18px;',
					                                        )) . ' <span class="badge ' . $class . '">' . $rating . '</span>',
				                                        'url'     => '#',
				                                        'visible' => !Yii::app()->getUser()->getIsGuest(),
				                                        'items'   => array(
					                                        array(
						                                        'label' => Yii::t('userModule.common', 'Профиль'),
						                                        'url'   => Yii::app()->getUser()->getUrl(),
					                                        ),
					                                        array(
						                                        'label' => Yii::t('pmsModule.common',
							                                        'Личные сообщения'),
						                                        'url'   => array('/pms/default/index'),
					                                        ),
					                                        array(
						                                        'label' => Yii::t('blogsModule.common', 'Мои блоги'),
						                                        'url'   => array('/blogs/default/my'),
					                                        ),
					                                        array(
						                                        'label' => Yii::t('groupsModule.common', 'Мои группы'),
						                                        'url'   => array('/groups/default/my'),
					                                        ),
					                                        array(
						                                        'label' => Yii::t('userModule.common', 'Настройки'),
						                                        'url'   => array('/user/default/settings'),
					                                        ),
					                                        array(
						                                        'label' => Yii::t('userModule.common', 'Выход'),
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
					                                        'id'          => 'eventsMenu',
					                                        'class'       => 'dropdown-toggle',
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