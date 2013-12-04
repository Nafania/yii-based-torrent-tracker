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
 * @method CActiveRecord getOwner()
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

		return Yii::app()->getBaseUrl($absolutePath) . '/' . ltrim($src, '/');
	}

	/*
	* @return string directory for uploaded image
	*/
	private function getImagePath () {
		$dir = $this->getParsedPath();

		if ( !is_dir($dir) ) {
			@mkdir($dir, 0777, true);
		}
		return $dir;
	}

	public function getFullAttachmentPath () {
		/**
		 * берем версию из базы, потому что новая при сохранении стирается
		 */
		return Yii::getPathOfAlias('webroot') . '/' . $this->getOwner()->findByPk($this->getOwner()->primaryKey)->{$this->attribute};
	}

	/**
	 * check if we have an attachment
	 */
	public function hasAttachment () {
		if ( $this->getOwner()->getIsNewRecord() ) {
			return false;
		}
		/**
		 * берем версию из базы, потому что новая при сохранении стирается
		 */
		return $this->getOwner()->findByPk($this->getOwner()->primaryKey)->{$this->attribute};
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

	public function beforeSave ( $e ) {
		parent::beforeSave($e);

		$file = CUploadedFile::getInstance($this->getOwner(), $this->attribute);

		if ( !empty($file->name) ) {
			$this->deleteAttachment();
		}
		else {
			unset($this->getOwner()->{$this->attribute});
		}

		return true;
	}

	/**
	 * @param CModelEvent $e
	 *
	 * @return bool|void
	 */
	public function afterSave ( $e ) {
		parent::afterSave($e);

		$owner = $this->getOwner();

		$file = CUploadedFile::getInstance($this->getOwner(), $this->attribute);

		if ( !empty($file->name) ) {
			$this->file_extension = mb_strtolower($file->getExtensionName());
			$this->filename = $file->getName();
			$path = $this->getParsedPath();

			preg_match('|^(.*[\\\/])|', $path, $match);
			$folder = end($match);
			if ( !is_dir($folder) ) {
				mkdir($folder, 0777, true);
			}

			if ( $file->saveAs(Yii::getPathOfAlias('webroot') . '/' . $path) ) {
				/**
				 * зачем менять флаг isNewRecord читайте тут http://code.google.com/p/yii/issues/detail?id=1603
				 */
				$owner->{$this->attribute} = $path;
				$isNewRecord = $owner->isNewRecord;
				$owner->isNewRecord = false;
				$owner->saveAttributes(array($this->attribute => $path));
				$owner->isNewRecord = $isNewRecord;
				//exit();
			}
		}

		return true;
	}

	public function beforeValidate ( $e ) {
		parent::beforeValidate($e);
		//CVarDumper::dump($this->getOwner(), 3, true);exit();

		if ( $file = CUploadedFile::getInstance($this->getOwner(), $this->attribute) ) {
			$this->getOwner()->{$this->attribute} = $file;

			$validator = CValidator::createValidator('file',
				$this->getOwner(),
				$this->attribute,
				array(
				     'allowEmpty' => $this->allowEmpty,
				     'types'      => $this->types,
				     'mimeTypes'  => $this->mimeTypes,
				     'maxFiles'   => $this->maxFiles,
				     'maxSize'    => $this->maxSize,
				     'minSize'    => $this->minSize
				));
			$this->getOwner()->getValidatorList()->insertAt(0, $validator);

			return true;
		}
		else {
			return true;
		}
	}

	public function afterValidate ( $e ) {
		parent::afterValidate($e);

		/**
		 * удаляем атрибут после валидации за тем, чтобы не произошло авто сохранения, а сработало наше сохранение из
		 * afterSave($e)
		 */
		unset($this->getOwner()->{$this->attribute});

		return true;
	}

	public function getParsedPath ( $custom = '' ) {
		$needle = array(
			':folder',
			':model',
			':id',
			':ext',
			':filename',
			':fileNameMd5',
			':custom',
			':firstTwoCharsMd5'
		);
		$replacement = array(
			$this->folder,
			get_class($this->Owner),
			$this->Owner->primaryKey,
			$this->file_extension,
			$this->filename,
			md5($this->filename),
			$custom,
			substr(md5($this->filename), 0, 2),
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
