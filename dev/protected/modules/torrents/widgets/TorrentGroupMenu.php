<?php
class TorrentGroupMenu extends CWidget {
	public $model;

	public function run () {
		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('subscriptions')->getAssetsUrl() . '/js/subscriptions.js');

		$this->render('torrentGroupMenu',
			array(
			     'items' => $this->_getItems(),
			     'model' => $this->model,
			));
	}

	private function _getItems () {
		if ( Subscription::check($this->model) ) {
			$subscribeItem = array(
				'label'       => '<i class="icon-minus-sign"></i>',
				'url'         => array('/subscriptions/default/delete'),
				'linkOptions' => array(
					'class'               => 'btn',
					'data-toggle'         => 'tooltip',
					'data-placement'      => 'top',
					'data-model'          => get_class($this->model),
					'data-id'             => $this->model->getId(),
					'data-action'         => 'subscription',
					'data-original-title' => Yii::t('torrentsModule.common',
						'Перестать следить за этой группой торрентов')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.delete'),
			);
		}
		else {
			$subscribeItem = array(
				'label'       => '<i class="icon-plus-sign"></i>',
				'url'         => array('/subscriptions/default/create'),
				'linkOptions' => array(
					'class'               => 'btn',
					'data-toggle'         => 'tooltip',
					'data-placement'      => 'top',
					'data-model'          => get_class($this->model),
					'data-id'             => $this->model->getId(),
					'data-action'         => 'subscription',
					'data-original-title' => Yii::t('torrentsModule.common',
						'Следить за этой группой торрентов')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('subscriptions.default.create'),
			);
		}

		return array(
			array(
				'label'       => '<i class="icon-upload"></i>',
				'url'         => array(
					'/torrents/default/createTorrent',
					'gId' => $this->model->getId()
				),
				'linkOptions' => array(
					'class'               => 'btn',
					'data-toggle'         => 'tooltip',
					'data-placement'      => 'top',
					'data-original-title' => Yii::t('torrentsModule.common',
						'Добавить торрент в группу')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('torrents.default.createTorrent'),
			),
			$subscribeItem,
			array(
				'label'       => '<i class="icon-edit"></i>',
				'url'         => array(
					'/torrents/default/updateGroup',
					'id' => $this->model->getId()
				),
				'linkOptions' => array(
					'class'               => 'btn',
					'data-toggle'         => 'tooltip',
					'data-placement'      => 'top',
					'data-original-title' => Yii::t('torrentsModule.common',
						'Редактировать группу торрентов')
				),
				'visible'     => Yii::app()->getUser()->checkAccess('torrents.default.updateGroup'),
			),
			array(
				'label'       => '<i class="icon-trash"></i>',
				'url'         => '#',
				'linkOptions' => array(
					'class'               => 'btn',
					'data-toggle'         => 'tooltip',
					'data-placement'      => 'top',
					'data-original-title' => Yii::t('torrentsModule.common',
						'Удалить группу торрентов'),
					'submit'              => array(
						'/torrents/default/delete',
						'id' => $this->model->getId()
					),
					'csrf'                => true,
					'confirm'             => Yii::t('torrentsModule.common',
						'Вы уверены, что хотите удалить эту группу торрентов?'),
				),
				'visible'     => Yii::app()->getUser()->checkAccess('torrents.default.delete'),
			),
		);

	}
}