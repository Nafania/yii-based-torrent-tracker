<?php
class CategoryBehavior extends CActiveRecordBehavior {
	//private $categoryAttributes;

	public function afterSave ( $e ) {
		parent::afterSave($e);

		if ( sizeof($this->getOwner()->categoryAttributes) ) {
			CategoryAttribute::model()->deleteAllByAttributes(array('catId' => $this->getOwner()->id));

			foreach ( $this->getOwner()->categoryAttributes AS $key => $val ) {
				$CategoryAttribute = new CategoryAttribute();
				$CategoryAttribute->attrId = $val;
				$CategoryAttribute->catId = $this->getOwner()->id;
				$CategoryAttribute->save();
			}

		}
		return true;
	}

	public function afterDelete () {
		CategoryAttribute::model()->deleteAllByAttributes(array('catId' => $this->getOwner()->id));
	}

	public function getCategoryAttributes () {

		$this->categoryAttributes = array();

		$cAttrs = CategoryAttribute::model()->forCat($this->getOwner()->id)->findAll();
		foreach ( $cAttrs AS $cAttr ) {
			$this->categoryAttributes[] = $cAttr->attrId;
		}

		return $this->categoryAttributes;
	}

	public function setCategoryAttributes ( $value ) {
		$this->categoryAttributes = $value;
	}
}