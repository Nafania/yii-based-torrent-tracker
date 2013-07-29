<?php
class SizeHelper {
	static function formatSize ( $size ) {

		if ( $size < 1024 * 1024 ) {
			return number_format($size / 1024, 2) . ' ' . Yii::t('size', 'KB');
		}
		elseif ( $size < 1024 * 1024 * 1024 ) {
			return number_format($size / (1024 * 1024), 2) . ' ' . Yii::t('size', 'MB');
		}
		elseif ( $size < 1024 * 1024 * 1024 * 1024 ) {
			return number_format($size / (1024 * 1024 * 1024), 2) . ' ' . Yii::t('size', 'GB');
		}
		else {
			return number_format($size / (1024 * 1024 * 1024 * 1024), 2) . ' ' . Yii::t('size', 'TB');
		}

	}
}
