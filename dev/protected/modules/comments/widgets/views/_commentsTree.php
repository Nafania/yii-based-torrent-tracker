<?php
/**
 * @var $comments Comment[]
 */
?>

<?php
foreach ( $comments as $comment ) {
	$this->render('application.modules.comments.widgets.views._commentView', array('comment' => $comment));
}
