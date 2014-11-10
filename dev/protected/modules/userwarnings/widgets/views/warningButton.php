<?php
/**
 * @var User $model
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile(Yii::app()->getModule('userwarnings')->getAssetsUrl() . '/js/warnings.js');
$cs->registerCoreScript('yiiactiveform');
?>
<?php $this->widget('bootstrap.widgets.TbButton',
	array(
		'buttonType'  => 'link',
		'type'        => 'danger',
		'label'       => Yii::t('userwarningsModule.common', 'Выдать предупреждение'),
		'url'         => Yii::app()->createUrl('/userwarnings/default/create'),
		'htmlOptions' => array(
			'data-uid'    => $model->getId(),
			'data-action' => 'warning'
		)
	));

$this->getController()->beginClip('afterContent');
$this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'warningModal')); ?>

<?php $this->endWidget(); ?>
<?php $this->getController()->endClip('afterContent'); ?>