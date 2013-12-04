<?php
/**
 * @var $models PrivateMessage[]
 * @var $pm     PrivateMessage
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile(Yii::app()->getModule('pms')->getAssetsUrl() . '/js/pms.js');
$cs->registerScript('pmsUrl', 'var pmsUrl=' . CJavaScript::encode(Yii::app()->createUrl('/pms/default/loadAnswerBlock')) . ';', CClientScript::POS_HEAD);
$cs->registerCssFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.css');
$cs->registerScriptFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.js');
?>

	<h1><?php echo Yii::t('pmsModule.common',
			'Личное сообщение "{title}"',
			array('{title}' => $models[0]->getTitle())); ?></h1>
<div class="messagesTree">
<?php
$this->renderPartial('_tree', array('models' => $models));
?>
</div>
<?php
$this->renderPartial('branchAnswer', array('model' => $pm));

$this->widget('application.modules.reports.widgets.ReportModal');
