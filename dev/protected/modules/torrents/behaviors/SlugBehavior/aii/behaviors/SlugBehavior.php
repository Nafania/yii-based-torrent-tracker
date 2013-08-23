<?php
/**
 * SlugBehavior class file.
 *
 * @author                                                Aleksey Konovalov <avalak.box@gmail.com>
 * @link                                                  http://avalak.net/
 * @copyright                                             Copyright &copy; 2010
 * @license                                               http://www.yiiframework.com/license/
 *
 * NOTE: Behavior use Google_Translate_API.php by @author gabe@fijiwebdesign.com
 */

/**
 * Google_Translate_API.php
 *
 * Translating language with Google API
 * @author  gabe@fijiwebdesign.com
 * @version $Id: google.translator.php 7 2009-08-20 09:30:43Z bucabay $
 * @license - Share-Alike 3.0 (http://creativecommons.org/licenses/by-sa/3.0/)
 *
 * Google requires attribution for their Language API, please see: http://code.google.com/apis/ajaxlanguage/documentation/#Branding
 */

/*
 * Usage example:
			'SlugBehavior' => array(
				'class' => 'ext.aii.behaviors.SlugBehavior',
				'sourceAttribute' => 'title',
				'slugAttribute' => 'slug',
				'mode' => 'translate',
			),
 *
 */
class SlugBehavior extends CActiveRecordBehavior {
	/**
	 * @var mixed The name of the attribute to store the modification time.  Set to null to not
	 * use a timstamp for the update attribute.  Defaults to 'update_time'
	 */
	const MODE_NUMBER = 'number'; // Dummy value
	const MODE_TRANSLIT = 'translit';
	const MODE_TRANSLATE = 'translate';
	const GOOGLE_API_PATH = 'ext.googleapi.Google_Translate_API';

	// source slug
	public $sourceAttribute = 'title';
	// result
	public $slugAttribute = 'slug';
	// api path
	public $googleApiPath = self::GOOGLE_API_PATH;
	//mode: translit / translate
	public $mode = self::MODE_TRANSLIT;

	public $connectionId = 'db';


	/**
	 * Данные для транслита
	 *
	 * @var array
	 */
	protected $replaceList = array(
		'э' => 'je',
		'ё' => 'jo',
		'я' => 'ya',
		'ю' => 'yu',
		'ы' => 'y',
		'ж' => 'zh',
		'й' => 'y',
		'щ' => 'shch',
		'ч' => 'ch',
		'ш' => 'sh',
		'э' => 'je',
		'а' => 'a',
		'б' => 'b',
		'в' => 'v',
		'г' => 'g',
		'д' => 'd',
		'е' => 'e',
		'з' => 'z',
		'и' => 'i',
		'к' => 'k',
		'л' => 'l',
		'м' => 'm',
		'н' => 'n',
		'о' => 'o',
		'п' => 'p',
		'р' => 'r',
		'с' => 's',
		'т' => 't',
		'у' => 'u',
		'ф' => 'f',
		'х' => 'h',
		'ц' => 'c',
		'ь' => '',
		'ъ' => '',
		'Э' => 'JE',
		'Ё' => 'JO',
		'Я' => 'YA',
		'Ю' => 'YU',
		'Ы' => 'Y',
		'Ж' => 'ZH',
		'Й' => 'Y',
		'Щ' => 'SHCH',
		'Ч' => 'CH',
		'Ш' => 'SH',
		'А' => 'A',
		'Б' => 'B',
		'В' => 'V',
		'Г' => 'G',
		'Д' => 'D',
		'Е' => 'E',
		'З' => 'Z',
		'И' => 'I',
		'К' => 'K',
		'Л' => 'L',
		'М' => 'M',
		'Н' => 'N',
		'О' => 'O',
		'П' => 'P',
		'Р' => 'R',
		'С' => 'S',
		'Т' => 'T',
		'У' => 'U',
		'Ф' => 'F',
		'Х' => 'H',
		'Ц' => 'C',
		'Ь' => '',
		'Ъ' => '',
	);

	public $cleanList = array(
		'`&([a-z]+)(acute|grave|circ|cedil|tilde|uml|lig|ring|caron|slash);`i' => '\1',
		'`&(amp;)?[^;]+;`i'                                                    => '-',
		'`[^a-z0-9]`i'                                                         => '-',
		'`[-]+`'                                                               => '-',
	);

	/**
	 * Responds to {@link CModel::onBeforeSave} event.
	 * Sets the values of the creation or modified attributes as configured
	 *
	 * @param CModelEvent event parameter
	 */
	/*public function beforeSave ( $event ) {
		if ( $this->getOwner()->isNewRecord ) {
			if ( $this->mode == self::MODE_TRANSLIT ) {
				$this->getOwner()->{$this->slugAttribute} = $this->convertToSlug($this->getOwner()->{$this->sourceAttribute});
			}
			else if ( $this->mode == self::MODE_TRANSLATE ) {
				$this->getOwner()->{$this->slugAttribute} = $this->translateSlug($this->getOwner()->{$this->sourceAttribute});
			}
		}
	}

	public function afterSave ( $event ) {
		// add "-$id" to slug string to prevent collision
		if ( $this->getOwner()->isNewRecord ) {
			$this->getOwner()->{$this->slugAttribute} = $this->getOwner()->{$this->slugAttribute} . "-" . Yii::app()->{$this->connectionId}->getLastInsertID();
			$this->getOwner()->isNewRecord = false;
			$this->getOwner()->update();
		}
	}*/

	public function getSlugTitle () {
		if ( $this->mode == self::MODE_TRANSLIT ) {
			return $this->convertToSlug($this->getOwner()->{$this->sourceAttribute});
		}
		else if ( $this->mode == self::MODE_TRANSLATE ) {
			return $this->translateSlug($this->getOwner()->{$this->sourceAttribute});
		}
	}

	/*
	 * Get translited 'slug'
	 * @param string title
	 * @return string slug
	 */
	protected function convertToSlug ( $source ) {
		$source = str_replace(array_keys($this->replaceList), array_values($this->replaceList), $source);
		$source = htmlentities($source, ENT_COMPAT, 'UTF-8');
		$source = preg_replace(array_keys($this->cleanList), array_values($this->cleanList), $source);
		$source = strtolower(trim($source, '-'));

		return $source;
	}

	/*
	 * Get translated 'slug'
	 * @param string title
	 * @return string slug
	 */
	protected function translateSlug ( $source ) {
		Yii::import($this->googleApiPath);
		$translated = Google_Translate_API::translate(strtr($source, $this->smileList));

		if ( isset($translated) ) {
			return $this->convertToSlug($translated);
		}

		return $source;
	}
}
