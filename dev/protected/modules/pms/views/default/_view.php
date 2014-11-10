<?php
/**
 * @var $model PrivateMessage
 */
?>

<div class="pmContainer">
	<div class="media well" id="message-<?php echo $model->getId(); ?>" data-id="<?php echo $model->getId(); ?>">

	<?php
	if ( $model->user ) {
		$img = $model->user->profile->getImageUrl(60, 60);
		$alt = $model->user->getName();
		$url = $model->user->getUrl();
	}
	else {
		$img = '/images/no_photo.png';
		$alt = '';
		$url = '';
	}
	echo CHtml::link(CHtml::image($img,
			$alt,
			array(
			     'class'  => 'media-object img-polaroid',
			     'width'  => '60',
			     'height' => '60'
			)),
		$url,
		array('class' => 'pull-left'));
	?>

		<div class="media-body">
		<div class="comment">
        <h6 class="media-heading">
	        <?php
	        if ( $model->user ) {
		        echo CHtml::link($model->user->getName(), $model->user->getUrl());
	        }
	        else {
		        echo '<i>' . Yii::t('userModule.common', 'Аккаунт удален') . '</i>';
	        }
	        ?>
	        , <abbr title="<?php echo Yii::app()->dateFormatter->formatDateTime($model->ctime); ?>"><?php echo TimeHelper::timeAgoInWords($model->ctime); ?></abbr>



	        <div class="pull-right">
	     				<?php if ( Yii::app()->getUser()->checkAccess('reports.default.create') ) {
	     					$this->widget('ext.bootstrap.widgets.TbButton',
	     						array(
	     						     'buttonType'  => 'link',
	     						     //'type'=> 'info',
	     						     'size'        => 'mini',
	     						     'icon'        => 'warning-sign',
	     						     'url'         => array('/reports/default/create/'),
	     						     'htmlOptions' => array(
	     							     'title'          => Yii::t('reportsModule.common',
	     								     'Пожаловаться на сообщение'),
	     							     'data-action'    => 'report',
	     							     'data-model'     => $model->resolveClassName(),
	     							     'data-id'        => $model->getId(),
	     							     'data-toggle'    => 'tooltip',
	     							     'data-placement' => 'top'
	     						     )
	     						));

	     				} ?>
	     				<?php if ( Yii::app()->getUser()->checkAccess('pms.default.create') ) {
	     					$this->widget('ext.bootstrap.widgets.TbButton',
	     						array(
	     						     'buttonType'  => 'link',
	     						     //'type'=> 'info',
	     						     'size'        => 'mini',
	     						     'icon'        => 'share-alt',
	     						     'htmlOptions' => array(
	     							     'title'          => Yii::t('pmsModule.common', 'Ответить'),
	     							     'data-placement' => 'top',
	     							     'data-toggle'    => 'tooltip',
	     							     'data-action'    => 'pm-answer'
	     						     )
	     						));

	     				} ?>

	     			</div>
	        </h6>

             <div class="commentText"><?php echo TextHelper::parseText($model->getMessage(),
		             'message-' . $model->getId()); ?></div>
		</div>

	</div>
		<div class="pull-right">
		<?php
		if ($model->readed == PrivateMessage::READED ) {
			echo '<i class="icon-eye-open" title="' . Yii::t('pmsModule.common', 'Сообщение прочитано') . '" data-placement="top" data-toggle="tooltip"></i>';
		}
		else {
			echo '<i class="icon-eye-close" title="' . Yii::t('pmsModule.common', 'Сообщение не прочитано') . '" data-placement="top" data-toggle="tooltip"></i>';
		}
		?>
		</div>
</div>

	<?php
	if ( count($model->childs) > 0 ) {
		$this->renderPartial('_tree',
			array(
			     'models' => $model->childs,
			));
	}
	?>
</div>