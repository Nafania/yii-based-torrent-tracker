<?php
$this->getController()->beginClip('afterContent');
$this->beginWidget('bootstrap.widgets.TbModal',
	array(
	     'id' => 'adultsOnly',
	     'autoOpen' => true,
	     'options' => array(
		     'keyboard' => false,
		     'backdrop' => 'static'
	     )
	)); ?>
<div class="modal-header">
			<h4><?php echo Yii::t('torrentsModule.common', 'Только 18+'); ?></h4>
		</div>

	<div class="modal-body">
			<p><?php echo Yii::t('torrentsModule.common',
					'Эта страница только для совершеннолетних, подтвердите, что вам исполнилось 18 лет.'); ?></p>
		</div>

	<div class="modal-footer">
			<button class="btn" onclick="javascript:history.back();"><?php echo Yii::t('torrentsModule.common',
					'Мне еще нет 18 лет'); ?></button>
			<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="document.cookie='AdultsWarning=false; path=/;'"><?php echo Yii::t('torrentsModule.common',
					'Мне уже есть 18 лет'); ?></button>
	</div>
<?php $this->endWidget();
$this->getController()->endClip('afterContent');?><!-- endmodal -->