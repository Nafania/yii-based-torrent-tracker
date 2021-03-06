<?php

class TorrentGroupMenu extends CWidget {
    /**
     * @var modules\torrents\models\TorrentGroup
     */
    public $model;

	public function run () {
		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('subscriptions')->getAssetsUrl() . '/js/subscriptions.js');
		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('favorites')->getAssetsUrl() . '/js/favorites.js');

		$this->render('torrentGroupMenu',
			array(
				'items' => $this->_getItems(),
				'model' => $this->model,
			));
	}

	private function _getItems () {
		if ( Subscription::check($this->model->resolveClassName(), $this->model->getPrimaryKey()) ) {
			$subscribeItem = array(
				'label'       => '<i class="icon-eye-close"></i>',
				'url'         => array('/subscriptions/default/delete'),
				'linkOptions' => array(
					'class'          => 'btn',
					'data-toggle'    => 'tooltip',
					'data-placement' => 'top',
					'data-model'     => $this->model->resolveClassName(),
					'data-id'        => $this->model->getId(),
					'data-action'    => 'subscription',
					'title'          => Yii::t('torrentsModule.common',
							'Перестать получать уведомления о новых торрентах в этой группе торрентов'),
                    'data-alt-title' => Yii::t('torrentsModule.common',
                            'Получать уведомления о новых торрентах в этой группе торрентов')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.delete'),
			);
		}
		else {
			$subscribeItem = array(
				'label'       => '<i class="icon-eye-open"></i>',
				'url'         => array('/subscriptions/default/create'),
				'linkOptions' => array(
					'class'          => 'btn',
					'data-toggle'    => 'tooltip',
					'data-placement' => 'top',
					'data-model'     => $this->model->resolveClassName(),
					'data-id'        => $this->model->getId(),
					'data-action'    => 'subscription',
					'title'          => Yii::t('torrentsModule.common',
							'Получать уведомления о новых торрентах в этой группе торрентов'),
                    'data-alt-title' => Yii::t('torrentsModule.common',
                            'Перестать получать уведомления о новых торрентах в этой группе торрентов')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.create'),
			);
		}

        if ( Subscription::check($this->model->resolveClassName() . '_comments', $this->model->getPrimaryKey()) ) {
            $subscribeCommentsItem = [
                'label'       => '<i class="icon-comment icon-white" data-alt-class="icon-comment"></i>',
                'url'         => ['/subscriptions/default/delete'],
                'linkOptions' => [
                    'class'          => 'btn',
                    'data-toggle'    => 'tooltip',
                    'data-placement' => 'top',
                    'data-model'     => $this->model->resolveClassName() . '_comments',
                    'data-id'        => $this->model->getPrimaryKey(),
                    'data-action'    => 'subscription',
                    'title'          => Yii::t('torrentsModule.common',
                            'Перестать получать уведомления о комментариях в этой группе торрентов'),
                    'data-alt-title' => Yii::t('torrentsModule.common',
                            'Получать уведомления о комментариях в этой группе торрентов')
                ],
                'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.delete'),
            ];
        }
        else {
            $subscribeCommentsItem = [
                'label'       => '<i class="icon-comment" data-alt-class="icon-comment icon-white"></i>',
                'url'         => ['/subscriptions/default/create'],
                'linkOptions' => [
                    'class'          => 'btn',
                    'data-toggle'    => 'tooltip',
                    'data-placement' => 'top',
                    'data-model'     => $this->model->resolveClassName() . '_comments',
                    'data-id'        => $this->model->getPrimaryKey(),
                    'data-action'    => 'subscription',
                    'title'          => Yii::t('torrentsModule.common',
                            'Получать уведомления о комментариях в этой группе торрентов'),
                    'data-alt-title' => Yii::t('torrentsModule.common',
                            'Перестать получать уведомления о комментариях в этой группе торрентов')
                ],
                'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.create'),
            ];
        }

		if ( Yii::app()->getUser()->checkAccess('favorites.default.delete') && $this->model->isFavorited() ) {
			$favoriteItem = array(
				'label'       => '<i class="icon-star"></i>',
				'url'         => array('/favorites/default/delete'),
				'linkOptions' => array(
					'class'          => 'btn',
					'data-toggle'    => 'tooltip',
					'data-placement' => 'top',
					'data-model'     => $this->model->resolveClassName(),
					'data-id'        => $this->model->getId(),
					'data-action'    => 'favorites',
					'title'          => Yii::t('favoritesModule.common',
							'Удалить из избранного')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('favorites.default.delete'),
			);
		}
		elseif ( Yii::app()->getUser()->checkAccess('favorites.default.create') ) {
			$favoriteItem = array(
				'label'       => '<i class="icon-star-empty"></i>',
				'url'         => array('/favorites/default/create'),
				'linkOptions' => array(
					'class'          => 'btn',
					'data-toggle'    => 'tooltip',
					'data-placement' => 'top',
					'data-model'     => $this->model->resolveClassName(),
					'data-id'        => $this->model->getId(),
					'data-action'    => 'favorites',
					'title'          => Yii::t('favoritesModule.common',
							'Добавить в избранное')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('favorites.default.create'),
			);
		}
		else {
			$favoriteItem = array();
		}

		return array(
			array(
				'label'       => '<i class="icon-upload"></i>',
				'url'         => array(
					'/torrents/default/createTorrent',
					'gId' => $this->model->getId()
				),
				'linkOptions' => array(
					'class'          => 'btn',
					'data-toggle'    => 'tooltip',
					'data-placement' => 'top',
					'title'          => Yii::t('torrentsModule.common',
							'Добавить торрент в группу')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('torrents.default.createTorrent'),
			),
			$subscribeItem,
            $subscribeCommentsItem,
			$favoriteItem,
			array(
				'label'       => '<i class="icon-edit"></i>',
				'url'         => array(
					'/torrents/default/updateGroup',
					'id' => $this->model->getId()
				),
				'linkOptions' => array(
					'class'          => 'btn',
					'data-toggle'    => 'tooltip',
					'data-placement' => 'top',
					'title'          => Yii::t('torrentsModule.common',
							'Редактировать группу торрентов')
				),
				'visible'     => Yii::app()->user->checkAccess('updateOwnTorrentGroup',
						array('model' => $this->model)) || Yii::app()->user->checkAccess('updateTorrentGroup'),
			),
			array(
				'label'       => '<i class="icon-trash"></i>',
				'url'         => '#',
				'linkOptions' => array(
					'class'          => 'btn',
					'data-toggle'    => 'tooltip',
					'data-placement' => 'top',
					'title'          => Yii::t('torrentsModule.common',
							'Удалить группу торрентов'),
					'submit'         => array(
						'/torrents/default/delete',
						'id' => $this->model->getId()
					),
					'csrf'           => true,
					'confirm'        => Yii::t('torrentsModule.common',
							'Вы уверены, что хотите удалить эту группу торрентов?'),
				),
				'visible'     => Yii::app()->getUser()->checkAccess('torrents.default.delete'),
			),
		);

	}
}