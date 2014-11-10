<?php
namespace modules\favorites\models;

use Yii;
use CValidator;

/**
 * This is the model class for table "favorites".
 *
 * The followings are the available columns in table 'favorites':
 * @property integer                                 $ctime
 * @property string                                  $modelName
 * @property integer                                 $modelId
 * @property integer                                 $uId
 */
class Favorite extends \EActiveRecord {
	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return \modules\favorites\models\Favorite the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'favorites';
	}


	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			$validator = CValidator::createValidator('unique',
				$this,
				'modelId',
				array(
					'criteria' => array(
						'condition' => 'modelName = :modelName AND uId = :uId',
						'params'    => array(
							':modelName' => $this->modelName,
							':uId'       => Yii::app()->getUser()->getId(),
						),
					),
					'message'  => Yii::t('favoritesModule.common', 'Такая запись в избранном уже существует.')
				));
			$this->getValidatorList()->insertAt(0, $validator);

			return true;
		}

		return false;
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {

			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->uId = Yii::app()->getUser()->getId();
			}

			return true;
		}

		return false;
	}
}