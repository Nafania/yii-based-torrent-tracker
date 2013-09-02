<?php

/**
 * This is the model class for table "categories".
 *
 * The followings are the available columns in table 'categories':
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $root
 * @property integer $level
 * @property string  $name
 * @property string  $picture
 * @property string  $description
 */
class Category extends EActiveRecord {

	public $cacheTime = 3600;

	//public $categoryAttributes = array();

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Category the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'categories';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return CMap::mergeArray(parent::rules(), array(
			array(
				'name',
				'safe'
			),
			array(
				'name',
				'length',
				'max' => 255
			),
			array(
				'description',
				'safe',
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, name, description',
				'safe',
				'on' => 'search'
			),
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'id'    => 'ID',
			'lft'   => 'Lft',
			'rgt'   => 'Rgt',
			'level' => 'Level',
			'name'  => 'Name',
		);
	}

	public function defaultScope() {
		return array(
			'order' => 'lft ASC'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search () {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('description', $this->description, true);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
		                                           'sort' => array(
			                                           'defaultOrder' => 't.lft ASC'
		                                           )
		                                      ));
	}

	public function getId () {
		return $this->id;
	}

	public function getTitle () {
		return $this->name;
	}

	public function getUrl() {
		return array(
			'/category/default/view', 'id' => $this->getId(),
		);
	}

	public function getTree ( $url = "" ) {
		$models = self::model()->findAll(array('order' => 'lft'));
		if ( count($models) == 0 ) {
			throw new CDbException(Yii::t('tree',
				'There must be minimum one root record in model `{model}`',
				array('{model}' => get_class(self::model()))));
		}
		$data = self::loadTree($models, $url);

		return ($data['data']);
	}

	/**
	 * Load items to an array recursively
	 *
	 * @param CActiveRecord $models
	 * @param string        $url       url to item href
	 * @param array         $NodeArray the recrusive array
	 *
	 * @return array of the item
	 */
	private function loadTree ( $models, $url = "", $NodeArray = null ) {
		$NodeArray['count'] = count($models);
		unset($NodeArray['data']);
		foreach ( $models as $i => $node ) {
			if ( !isset($NodeArray['pk']) || !in_array($node->primaryKey, $NodeArray['pk']) ) {
				$NodeArray['pk'][] = $node->primaryKey;
				$NodeArray['data'][$i] = array(
					'key'     => $node->primaryKey,
					'id'      => $node->primaryKey,
					'title'   => $node->name,
					'tooltip' => $node->name,
				);
				if ( $url ) {
					$NodeArray['data'][$i]['href'] = $url . $node->primaryKey;
				}
				if ( !$node->isLeaf() ) {
					$inter = self::loadTree($node->children()->findAll(), $url, $NodeArray);
					$NodeArray['pk'] = $inter['pk'];
					$NodeArray['data'][$i]['isFolder'] = 'true';
					$NodeArray['data'][$i]['children'] = $inter['data'];
				}
				if ( $node->isRoot() ) {
					$NodeArray['data'][$i]['isFolder'] = 'true';
				}
			}
		}
		return $NodeArray;
	}
}