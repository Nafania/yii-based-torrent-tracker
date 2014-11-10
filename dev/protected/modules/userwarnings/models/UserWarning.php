<?php
namespace modules\userwarnings\models;

use Yii;

/**
 * This is the model class for table "favorites".
 *
 * The followings are the available columns in table 'favorites':
 * @property integer                                 $id
 * @property integer                                 $ctime
 * @property integer                                 $uId
 * @property integer                                 $fromUid
 * @property string                                  $text
 * @property User                                    $user
 */
class UserWarning extends \EActiveRecord {
	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return \modules\userwarnings\models\UserWarning the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'userWarnings';
	}

	/**
	 * @return array
	 */
	public function rules () {
		return \CMap::mergeArray(parent::rules(),
			array(
				array(
					'text',
					'required',
				),
				array(
					'text',
					'filter',
					'filter' => array(
						new \CHtmlPurifier(),
						'purify'
					)
				)
			));
	}

	/**
	 * @return array
	 */
	public function relations () {
		return \CMap::mergeArray(parent::relations(),
			array(
				'fromUser' => array(
					self::BELONGS_TO,
					'User',
					'fromUid'
				),
			),
			array(
				'user' => array(
					self::BELONGS_TO,
					'User',
					'uId'
				),
			));
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'    => 'ID',
			'ctime' => Yii::t('userwarningsModule.common', 'Добавлено'),
			'text'  => Yii::t('userwarningsModule.common', 'Текст предупреждения'),
		);
	}

	/**
	 * @return bool
	 */
	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			$validator = \CValidator::createValidator('exist',
				$this,
				'uId',
				array(
					'attributeName' => 'id',
					'className'     => 'User',
					'allowEmpty'    => false,
				));
			$this->getValidatorList()->insertAt(0, $validator);

			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	protected function beforeSave () {
		if ( parent::beforeSave() ) {

			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->fromUid = \Yii::app()->getUser()->getId();
			}

			return true;
		}

		return false;
	}

	/**
	 * @param bool $encode
	 *
	 * @return string
	 */
	public function getText ( $encode = true ) {
		if ( $encode ) {
			return \CHtml::encode($this->text);
		}
		else {
			return $this->text;
		}
	}


	/**
	 * @param bool $encode
	 *
	 * @return string
	 */
	public function getFullText ( $encode = true ) {
		$roles = Yii::app()->getAuthManager()->getRoles($this->fromUser->getId());
		$rolesStr = '';
		foreach ( $roles AS $role ) {
			$rolesStr .= ($rolesStr ? ', ' : '') . $role->getDescription();
		}
		$text = Yii::t('userwarningsModule.common',
			'Предупреждение от {userName} ({roles}) по причине "{text}" получено {dateTime}',
			array(
				'{userName}' => $this->fromUser->getName(),
				'{text}'     => $this->text,
				'{dateTime}' => \Yii::app()->getDateFormatter()->formatDateTime($this->ctime),
				'{roles}'    => $rolesStr
			));

		return ( $encode ? \CHtml::encode($text) : $text );
	}
}