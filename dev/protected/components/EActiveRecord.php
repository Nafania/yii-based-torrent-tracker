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
			Yii::trace('Model ' . get_class($this) . ' cache cleared at ' . date('d.m.Y H:i:s'));
			Yii::app()->cache->set($this->getCacheKey(), microtime(true), 0);
		}

		parent::afterSave();
	}

	protected function beforeFind () {
		if ( $this->cacheTime ) {
			$dependency = new CTagCacheDependency($this->getCacheKey());
			$this->cache($this->cacheTime, $dependency);
			Yii::trace('Model ' . get_class($this) . ' cached for ' . $this->cacheTime . ' seconds at ' . date('d.m.Y H:i:s') . ' last change at ' . date('d.m.Y H:i:s', $dependency->generateDependentData()));
		}

		return parent::beforeFind();
	}

	protected function afterDelete () {
		if ( $this->cacheTime ) {
			Yii::app()->cache->set($this->getCacheKey(), microtime(true), 0);
			Yii::trace('Model ' . get_class($this) . ' cache cleared at ' . date('d.m.Y H:i:s'));
		}

		return parent::afterDelete();
	}

	private function getCacheKey () {
		return 'EActiveRecordModelCache'  . get_class($this);
	}
}