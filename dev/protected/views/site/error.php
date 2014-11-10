<?php
/* @var $this SiteController */
/* @var $error array */
?>

<?php
	switch ( $code ) {
		case 404:
			echo '<p><img src="/images/Hypno_large.gif" /></p>';
			echo '<p>Слушай гипножабу, страницы этой не существует, истинно тебе говорю</p>';
		break;

		default:
			echo '<h2>Error ' . $code . '</h2>';
			echo CHtml::encode($message);
		break;
	}
?>