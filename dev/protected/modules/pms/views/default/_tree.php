<?php
/**
 * @var $models PrivateMessage[]
 * @var $pm     PrivateMessage
 */
?>
<?php
$size = sizeof($models);
foreach ( $models as $i => $model ) {
	$this->renderPartial('_view', array('model' => $model));
}