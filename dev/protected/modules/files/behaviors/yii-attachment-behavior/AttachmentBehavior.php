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
		if ( $this->Owner->{$this->attribute} ) {
			$src = $this->Owner->{$this->attribute};
		}
		elseif ( $this->fallback_image ) {
			$src = $this->fallback_image;
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
		else {
			$src = '/' . $src;
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

	/**
	 * check if we have an attachment
	 */
	public function hasAttachment () {
		return file_exists($this->Owner->{$this->attribute});
	}

	/**
	 * deletes the attachment
	 */
	public function deleteAttachment () {
		if ( file_exists($this->Owner->{$this->attribute}) ) {
			unlink($this->Owner->{$this->attribute});
		}
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

	public function afterDelete ( $event ) {
		$this->deleteAttachment();
	}

	public function beforeValidate ( $e ) {
		parent::beforeValidate($e);

		if ( CUploadedFile::getInstance($this->getOwner(), $this->attribute) ) {
			$this->getOwner()->{$this->attribute} = md5(time());
		}
		return true;
	}

	public function beforeSave ( $e ) {
		$this->getOwner()->{$this->holdAttributeName} = $this->getOwner()->{$this->attribute};
		unset($this->getOwner()->{$this->attribute});

		return true;
	}


	public function afterSave ( $event ) {
		$file = AttachmentUploadedFile::getInstance($this->getOwner(), $this->attribute);
		if ( !is_null($file) ) {
			if ( !$this->Owner->isNewRecord ) {
				//delete previous attachment
				if ( file_exists($this->getOwner()->{$this->attribute}) ) {
					unlink($this->getOwner()->{$this->attribute});
				}
			}
			else {
				$this->Owner->isNewRecord = false;
			}
			preg_match('/\.(.*)$/', $file->name, $matches);
			$this->file_extension = end($matches);
			$this->filename = $file->name;
			$path = $this->parsedPath;

			preg_match('|^(.*[\\\/])|', $path, $match);
			$folder = end($match);
			if ( !is_dir($folder) ) {
				mkdir($folder, 0777, true);
			}

			$file->saveAs($path, false);
			$file_type = filetype($path);
			$this->Owner->saveAttributes(array($this->attribute => $path));
			$attributes = $this->Owner->attributes;

			if ( array_key_exists('file_size', $attributes) ) {
				$this->Owner->saveAttributes(array('file_size' => filesize($path)));
			}
			if ( array_key_exists('file_type', $attributes) ) {
				$this->Owner->saveAttributes(array('file_type' => mime_content_type($path)));
			}
			if ( array_key_exists('extension', $attributes) ) {
				$this->Owner->saveAttributes(array('extension' => $this->file_extension));
			}
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

class AttachmentUploadedFile {
	public $name, $_error;

	public static function getInstance ( $model, $attribute ) {
		$c = new AttachmentUploadedFile;
		$c->modelName = get_class($model);
		if ( empty($_FILES[$c->modelName]) || empty($_FILES[$c->modelName]['name'][$attribute]) ) {
			return null;
		}
		$c->name = $_FILES[$c->modelName]['name'][$attribute];
		$c->file_name = $_FILES[$c->modelName]['tmp_name'][$attribute];
		if ( !file_exists($c->file_name) ) {
			return null;
		}
		return $c;
	}

	public function saveAs ( $file ) {
		if ( $this->_error == UPLOAD_ERR_OK ) {
			if ( is_uploaded_file($this->file_name) ) {
				return move_uploaded_file($this->file_name, $file);
			}
			else {
				return rename($this->file_name, $file);
			}
		}
		else {
			return false;
		}
	}
}
