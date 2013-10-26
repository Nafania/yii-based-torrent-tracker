<?php
/**
 * @var $data Group
 */
?>
<div class="media groupsList">
	<?php
	$img = CHtml::image($data->getImageUrl(80, 80),
		$data->getTitle(),
		array(
		     'class' => 'media-object img-polaroid',
		     'style' => 'width:80px'
		));
	echo CHtml::link($img, $data->getUrl(), array('class' => 'pull-left'));
	?>

	<div class="media-body pull-left">
        <h3 class="media-heading"><?php echo CHtml::link($data->getTitle(), $data->getUrl()) ?></h3>
		<p><?php echo Yii::t('groupsModule.common',
					'Участники группы:') . ' ' . CHtml::link(Yii::t('groupsModule.common',
						'{n} человек|{n} человека',
						$data->groupUsersCount()),
					array(
					     '/groups/default/members',
					     'id' => $data->getId()
					)) ?>,
			<?php echo Yii::t('groupsModule.common',
				'Тип: {statusTitle}',
				array('{statusTitle}' => $data->getTypeLabel())) ?>

		</p>
        <p><?php echo StringHelper::cutStr($data->getDescription()); ?></p>
    </div>
	<div class="pull-right">
		<?php $this->widget('application.modules.groups.widgets.GroupOperations',
			array(
			     'group' => $data
			)); ?>
	</div>
</div>
<hr />