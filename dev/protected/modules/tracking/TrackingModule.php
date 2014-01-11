<?php
namespace modules\tracking;
use Yii;

class TrackingModule extends \CWebModule {
	public static function register () {
		self::_addBehaviors();
	}

	private static function _addBehaviors () {
		Yii::app()->pd->registerBehavior('modules\torrents\models\Torrent',
			array(
				'tracking' => array(
					'class' => 'application.modules.tracking.behaviors.TrackingBehavior'
				)
			));

		Yii::app()->pd->registerBehavior('Comment',
			array(
				'tracking' => array(
					'class' => 'application.modules.tracking.behaviors.TrackingBehavior'
				)
			));
	}
}
