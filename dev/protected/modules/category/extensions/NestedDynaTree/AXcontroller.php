<?php

/**
 * NestedDynaTreeController for handle AJAX request
 * for dynaTree jQuery plugin
 *
 * AJAX call have to contain the name of the model
 *
 * @author Szincsák András <andras@szincsak.hu>
 */
class AXcontroller extends CController {

	/**
	 * The most important part of controller.
	 * Check wether model name is given in the post
	 * and check model has nested behaviors
	 *
	 * @return string Model name or boolean  false if model have not nestedSet
	 */
	private function getModel () {

		if ( isset($_POST['model']) ) {
			$var = new $_POST['model']; //Model exists
			if ( !($var->roots()) ) {
				throw new CDbException(Yii::t('tree',
					'Model `' . $_POST['model'] . '` have to be an instance of NestedTreeActiveRecord, or have NestedSet behavior.'));
			}
			else {
				return $_POST['model'];
			}
		}
		return false;
	}

	/**
	 * AJAX load
	 * Check modelclass and load tree data
	 */
	public function actionLoad () {
		if ( ($modelClass = $this->getModel()) !== false ) {
			$url = (isset($_REQUEST['clickAction'])) ? $_REQUEST['clickAction'] : "";
			echo json_encode($modelClass::model()->getTree($url));
		}
		else {
			echo 0;
		}
	}

	/**
	 * AJAX Insert
	 */
	public function actionInsert () {

		if ( ($modelClass = $this->getModel()) !== false ) {
			$target = $modelClass::model()->findByPk($_POST['source']);
			$node = new $modelClass;

			try {
				if ( $target->isRoot() ) {
					$status = $node->appendTo($target, false);
				}
				else {
					switch ( $_POST['mode'] ) {
						case "after":
							$status = $node->insertAfter($target, false);
							break;
						case "over":
							$status = $node->moveAsLast($target);
							break;
					}
				}
				echo json_encode(array(
				                      'status'  => ($status === true) ? 1 : 0,
				                      'node'    => $target->primaryKey,
				                      'title'   => $node->nodeTitle,
				                      'key'     => $node->primaryKey
				                 ));
			} catch ( Exception $e ) {
				echo json_encode(array('status' => $e->getMessage()));
			}
		}
		else {
			echo 0;
		}
	}

	/**
	 * AJAX Move
	 */
	public function actionMove () {

		if ( ($modelClass = $this->getModel()) !== false ) {
			$node = $modelClass::model()->findByPk($_POST['source']);
			$target = $modelClass::model()->findByPk($_POST['target']);
			try {
				switch ( $_POST['mode'] ) {
					case "before":
						$status = $node->moveBefore($target);
						break;
					case "after":
						$status = $node->moveAfter($target);
						break;
					case "over":
						$status = $node->moveAsLast($target);
						break;
				}
				echo json_encode(array(
				                      'status'     => ($status) ? 1 : $status,
				                      'sourceNode' => $node->primaryKey,
				                      'node'       => $target->primaryKey,
				                      'mode'       => $_POST['mode']
				                 ));
			} catch ( Exception $e ) {
				echo json_encode(array('status' => $e->getMessage()));
			}
		}
		else {
			echo 0;
		}
	}

	/**
	 * AJAX delete
	 */
	public function actionDelete () {

		if ( ($modelClass = $this->getModel()) !== false ) {
			$target = $modelClass::model()->findByPk($_POST['source']);
			if ( $target->isRoot() ) {
				echo json_encode(array('status' => Yii::t('tree', 'root cannot be deleted!')));
				exit;
			}
			$prevnode = $target->next()->find();
			if ( !$prevnode ) {
				$prevnode = $target->prev()->find();
			}
			if ( !$prevnode ) {
				$prevnode = $target->parent()->find();
			}
			try {
				$status = $target->deleteNode();
				echo json_encode(array(
				                      'status'  => ($status === true) ? 1 : 0,
				                      'node'    => $target->primaryKey,
				                      'key'     => $prevnode->primaryKey
				                 ));
			} catch ( Exception $e ) {
				echo json_encode(array('status' => $e->getMessage()));
			}
		}
		else {
			echo 0;
		}
	}

}

?>
