<?php
/**
 * Image helper functions
 *
 * @author Chris
 */
Yii::import('application.extensions.ImageHandler');
class ImageHelper {

	/**
	 * Create a thumbnail of an image and returns relative path in webroot
	 *
	 * @param int    $width
	 * @param int    $height
	 * @param string $img
	 * @param int    $quality
	 *
	 * @return string $path
	 */
	public static function thumb ( $width, $height, $img, $quality = 80 ) {
		$thumbsPath = Yii::getPathOfAlias('webroot') . '/uploads/images/.thumbs/' . $width . 'x' . $height . '/';
		if ( !is_dir($thumbsPath) ) {
			mkdir($thumbsPath, 0777, true);
		}

		$pathInfo = pathinfo($img);
		$prefix = str_replace(array(Yii::getPathOfAlias('webroot'), '/', '\\', ':'), array('', '_', '_', ''), $pathInfo['dirname']);

		$thumbName = 'thumb_' . $prefix . '_' . $pathInfo['filename'] . '_' . $width . '_' . $height . '.' . $pathInfo['extension'];


		if ( !file_exists($thumbsPath . $thumbName) ) {
			Yii::app()->image->load($img)->adaptiveThumb($width, $height)->save($thumbsPath . $thumbName, false, $quality);
		}

		return str_replace(Yii::getPathOfAlias('webroot'), '', Yii::app()->getBaseUrl() . $thumbsPath . $thumbName);
	}
}