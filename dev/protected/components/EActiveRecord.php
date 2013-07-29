<?php
class EActiveRecord extends CActiveRecord {

	public $cacheTime = 0;

	public function init() {
		parent::init();

		return Yii::app()->pd->loadEvents($this);
	}

	public function behaviors () {
		return Yii::app()->pd->loadBehaviors($this);
	}

	public function relations() {
		return Yii::app()->pd->loadRelations($this);
	}

	public function rules() {
		return Yii::app()->pd->loadModelRules($this);
	}

	protected function afterSave () {
		if ( $this->cacheTime ) {
			Yii::trace('Model ' . get_class($this) . ' cache cleared');
			Yii::app()->cache->set(get_class($this), time(), 0);
		}

		parent::afterSave();
	}

	protected function beforeFind () {
		if ( $this->cacheTime ) {
			$this->cache($this->cacheTime, new CTagCacheDependency(get_class($this)));
			Yii::trace('Model ' . get_class($this) . ' cached for ' . $this->cacheTime . ' seconds');
		}

		return parent::beforeFind();
	}

	protected function afterDelete () {
		if ( $this->cacheTime ) {
			Yii::app()->cache->set(get_class($this), time(), 0);
			Yii::trace('Model ' . get_class($this) . ' cache cleared');
		}

		return parent::afterDelete();
	}
}