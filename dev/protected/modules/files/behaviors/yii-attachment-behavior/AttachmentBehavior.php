<?php
/**
 * AttachmentBehavior class file.
 *
 * @author    Greg Molnar
 * @link      https://github.com/gregmolnar/yii-attachment-behavior/
 * @copyright Copyright &copy; Greg Molnar
 * @license   http://opensource.org/licenses/bsd-license.php
 */

/**
 * This behaviour will let you add attachments to your model  easily
 * you will need to add the following database fields to your model:
 * filename string
 * In your model behaviours:
 *
 *    'image' => array(
 *               'class' => 'ext.AttachmentBehavior.AttachmentBehavior',
 *               'attribute' => 'filename',
 *               //'fallback_image' => 'images/sample_image.gif',
 *               'path' => "uploads/:model/:id.:ext",
 *               'processors' => array(
 *                   array(
 *                       'class' => 'ImagickProcessor',
 *                       'method' => 'resize',
 *                       'params' => array(
 *                           'width' => 310,
 *                           'height' => 150,
 *                           'keepratio' => true
 *                       )
 *                   )
 *               ),
 *               'styles' => array(
 *                   'thumb' => '!100x60',
 *               )
 *           ),
 *
 * @property string $path
 * @private string $filename
 * @private integer $filesize
 * @private string $parsedPath
 * */
class AttachmentBehavior extends CActiveRecordBehavior {

	/**
	 * @property string folder to save the attachment
	 */
	public $folder = 'uploads';

	/**
	 * @property string path to save the attachment
	 */
	public $path = ':folder/:id.:ext';

	/**
	 * @property name of attribute which holds the attachment
	 */
	public $attribute = 'filename';

	/**
	 * @property array of processors
	 */
	public $processors = array();

	public $types = null;
	public $mimeTypes = null;
	public $minSize;
	public $maxSize;
	public $maxFiles = 1;
	public $allowEmpty = true;

	/**
	 * @property array of styles needs to create
	 * example:
	 * array(
	 *   'small' => '150x75',
	 *  'medium' => '!250x70'
	 * )
	 */
	public $styles = array();

	/**
	 * @property string $fallback_image placeholder image src.
	 */
	public $fallback_image;

	private $file_extension, $filename;

	private $holdAttributeName = 'holdAttribute';

	public function getHoldAttribute () {
		return $this->getOwner()->{$this->holdAttributeName};
	}

	public function setHoldAttribute ( $value ) {
		$this->getOwner()->{$this->holdAttributeName} = $value;
	}

	/**
	 * getter method for the attachment.
	 * if you call it like a property ($model->Attachment) it will return the base size.
	 * if you have the styles specified you can get them like this:
	 * $model->getAttachment('small')
	 *
	 * @param string $style style to return
	 */
	public function getAttachment ( $style = '' ) {
		if ( $style == '' ) {
			if ( $this->hasAttachment() ) {
				return $this->Owner->{$this->attribute};
			}
			elseif ( $this->fallback_image != '' ) {
				return $this->fallback_image;
			}
			else {
				return '';
			}
		}
		else {
			if ( isset($this->styles[$style]) ) {
				$im = preg_replace('/\.(.*)$/', '-' . $style . '\\0', $this->Owner->{$this->attribute});
				if ( file_exists($im) ) {
					return $im;
				}
				elseif ( isset($this->fallback_image) ) {
					return $this->fallback_image;
				}
			}
		}

	}

	/**
	 *
	 * @param int  $width
	 * @param int  $height
	 * @param bool $absolutePath
	 *
	 * @return string image src
	 */
	public function getImageUrl ( $width = 0, $height = 0, $absolutePath = false ) {
		$width = (!$width ? false : $width);
		$height = (!$height ? false : $height);

		if ( $this->getOwner()->{$this->attribute} ) {
			$src = $this->getOwner()->{$this->attribute};
		}
		else {
			$src = '';
		}

		if ( $width && $height && $src ) {
			try {
				Yii::import('application.modules.files.helpers.*');
				$src = ImageHelper::adaptiveThumb($width, $height, $src);
			} catch ( Exception $exc ) {
				Yii::log('Cant convert image ' . $this->getImagePath() . ' with error ' . $exc->getMessage(),
					'warning');
			}
		}
		elseif ( (!$width || !$height) && $src ) {
			try {
				Yii::import('application.modules.files.helpers.*');
				$src = ImageHelper::thumb($width, $height, $src);
			} catch ( Exception $exc ) {
				Yii::log('Cant convert image ' . $this->getImagePath() . ' with error ' . $exc->getMessage(),
					'warning');
			}
		}
		else {
			$src = ($src ? '/' . $src : $this->fallback_image);
		}

		return Yii::app()->getBaseUrl($absolutePath) . $src;
	}

	/*
	* @return string directory for uploaded image
	*/
	private function getImagePath () {
		$dir = $this->getParsedPath();

		if ( !is_dir($dir) ) {
			mkdir($dir, 0777, true);
		}
		return $dir;
	}

	public function getFullAttachmentPath () {
		return Yii::getPathOfAlias('webroot') . '/' . $this->getOwner()->{$this->attribute};
	}

	/**
	 * check if we have an attachment
	 */
	public function hasAttachment () {
		if ( $this->getOwner()->getIsNewRecord() ) {
			return false;
		}
		return $this->getOwner()->{$this->attribute};
	}

	/**
	 * deletes the attachment
	 */
	public function deleteAttachment () {
		if ( $this->hasAttachment() ) {
			@unlink($this->getFullAttachmentPath());

			preg_match('/\.(.*)$/', $this->Owner->{$this->attribute}, $matches);
			$this->file_extension = end($matches);
			if ( !empty($this->styles) ) {
				$this->path = str_replace('.:ext', '-:custom.:ext', $this->path);
				foreach ( $this->styles as $style => $size ) {
					$path = $this->getParsedPath($style);
					if ( file_exists($path) ) {
						unlink($path);
					}
				}
			}
		}
	}

	public function afterDelete ( $event ) {
		parent::afterDelete($event);

		$this->deleteAttachment();
	}

	/*public function beforeValidate ( $e ) {
		parent::beforeValidate($e);

		if ( CUploadedFile::getInstance($this->getOwner(), $this->attribute) ) {
			$this->getOwner()->{$this->attribute} = md5(time());
		}
		return true;
	}*/

	public function beforeSave ( $e ) {
		parent::beforeSave($e);

		$file = CUploadedFile::getInstance($this->getOwner(), $this->attribute);

		if ( !empty($file->name) ) {
			$this->file_extension = $file->getExtensionName();
			$this->filename = $file->getName();
			$path = $this->getParsedPath();

			preg_match('|^(.*[\\\/])|', $path, $match);
			$folder = end($match);
			if ( !is_dir($folder) ) {
				mkdir($folder, 0777, true);
			}

			if ( $file->saveAs($path) ) {
				//do not use deleteAttachment here, cause attribute empty in beforeSave
				if ( !$this->getOwner()->getIsNewRecord() ) {
					$model = $this->getOwner()->findByPk($this->getOwner()->getPrimaryKey());
					@unlink($model->{$this->attribute});
				}

				$this->deleteAttachment();
				$this->getOwner()->{$this->attribute} = $path;
			}
			else {
				return false;
			}
		}
		else {
			unset($this->getOwner()->{$this->attribute});
		}

		return true;
	}

	public function afterValidate ( $e ) {
		parent::afterValidate($e);
		//CVarDumper::dump($this->getOwner(), 3, true);exit();

		$CFileValidator = new CFileValidator();
		$CFileValidator->allowEmpty = $this->allowEmpty;
		$CFileValidator->types = $this->types;
		$CFileValidator->mimeTypes = $this->mimeTypes;
		$CFileValidator->maxFiles = $this->maxFiles;
		$CFileValidator->maxSize = $this->maxSize;
		$CFileValidator->minSize = $this->minSize;

		$CFileValidator->attributes = array($this->attribute);
		$CFileValidator->validate($this->getOwner());

		return ($this->getOwner()->getError($this->attribute)) ? false : true;
	}

	public function getParsedPath ( $custom = '' ) {
		$needle = array(
			':folder',
			':model',
			':id',
			':ext',
			':filename',
			':fileNameMd5',
			':custom'
		);
		$replacement = array(
			$this->folder,
			get_class($this->Owner),
			$this->Owner->primaryKey,
			$this->file_extension,
			$this->filename,
			md5($this->filename),
			$custom,
		);
		if ( preg_match_all('/:\\{([^\\}]+)\\}/', $this->path, $matches, PREG_SET_ORDER) ) {
			foreach ( $matches as $match ) {
				$valuePath = explode('.', $match[1]);
				$value = $this->owner;
				foreach ( $valuePath as $attributeName ) {
					if ( is_object($value) ) {
						$value = $value->{$attributeName};
					}
				}
				$needle[] = $match[0];
				$replacement[] = $value;
			}
		}
		return str_replace($needle, $replacement, $this->path);
	}


	public function UnsafeAttribute ( $name, $value ) {
		var_dump(true);
		exit;
		if ( $name != $this->attribute ) {
			parent::onUnsafeAttribute($name, $value);
		}
	}
}
