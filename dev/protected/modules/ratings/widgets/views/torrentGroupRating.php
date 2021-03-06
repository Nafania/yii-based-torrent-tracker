<?php if (!$onlyBar) { ?>
<div class="torrentGroupRating">
<a class="btn" data-toggle="tooltip" data-placement="top" title="<?php echo Yii::t('ratingsModule.common',
	'Повысить рейтинг торрента'); ?>" data-action="rating" data-id="<?php echo $modelId ?>" data-model="<?php echo $modelName ?>" data-state="<?php echo RatingRelations::RATING_STATE_PLUS ?>" href="<?php echo Yii::app()->createUrl('ratings/default/create') ?>"><i class="icon-thumbs-up"></i></a>
<a class="btn" data-toggle="tooltip" data-placement="top" title="<?php echo Yii::t('ratingsModule.common',
	'Понизить рейтинг торрента'); ?>" data-action="rating" data-id="<?php echo $modelId ?>" data-model="<?php echo $modelName ?>" data-state="<?php echo RatingRelations::RATING_STATE_MINUS ?>" href="<?php echo Yii::app()->createUrl('ratings/default/create') ?>"><i class="icon-thumbs-down"></i></a>
	<?php } ?>

	<?php
	$this->widget('bootstrap.widgets.TbProgress',
		array(
			'stacked' => array(
				array(
					'type'        => 'success',
					'percent'     => str_replace(',', '.', $positivePercents),
					'htmlOptions' => array(
						'data-toggle'    => 'tooltip',
						'data-placement' => 'top',
						'title'          => Yii::t('ratingsModule.common',
								'Положительный рейтинг {rating}',
								array('{rating}' => $positiveRating)),
					)
				),
				array(
					'type'        => 'danger',
					'percent'     => str_replace(',', '.', $negativePercents),
					'htmlOptions' => array(
						'data-toggle'    => 'tooltip',
						'data-placement' => 'top',
						'title'          => Yii::t('ratingsModule.common',
								'Отрицательный рейтинг {rating}',
								array('{rating}' => $negativeRating)),
					)
				),
			)
		));
	?>
</div>