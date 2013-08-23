<div class="torrentGroupRating">
<a class="btn" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('ratingsModule.common',
	'Повысить рейтинг торрента'); ?>" data-action="rating" data-id="<?php echo $modelId ?>" data-model="<?php echo $modelName ?>" data-state="<?php echo RatingRelations::RATING_STATE_PLUS ?>" href="<?php echo Yii::app()->createUrl('ratings/default/create') ?>"><i class="icon-thumbs-up"></i></a>
<a class="btn" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('ratingsModule.common',
	'Понизить рейтинг торрента'); ?>" data-action="rating" data-id="<?php echo $modelId ?>" data-model="<?php echo $modelName ?>" data-state="<?php echo RatingRelations::RATING_STATE_MINUS ?>" href="<?php echo Yii::app()->createUrl('ratings/default/create') ?>"><i class="icon-thumbs-down"></i></a>

	<?php
	$this->widget('bootstrap.widgets.TbProgress',
		array(
		     'stacked' => array(
			     array(
				     'type'        => 'success',
				     'percent'     => $positivePercents,
				     'htmlOptions' => array(
					     'data-toggle'         => 'tooltip',
					     'data-placement'      => 'top',
					     'data-original-title' => Yii::t('ratingsModule.common',
						     'Positive rating is {rating}',
						     array('{rating}' => $positiveRating)),
				     )
			     ),
			     array(
				     'type'        => 'danger',
				     'percent'     => $negativePercents,
				     'htmlOptions' => array(
					     'data-toggle'         => 'tooltip',
					     'data-placement'      => 'top',
					     'data-original-title' => Yii::t('ratingsModule.common',
						     'Negative rating is {rating}',
						     array('{rating}' => $negativeRating)),
				     )
			     ),
		     )
		));
	?>
</div>