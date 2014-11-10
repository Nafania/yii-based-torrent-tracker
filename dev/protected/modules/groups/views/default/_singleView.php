<?php
/* @var $model Group */
?>
<div class="media groupView">
<?php
$img = CHtml::image($model->getImageUrl(100, 100),
	$model->getTitle(),
	array(
	     'class' => 'media-object img-polaroid',
	     'style' => 'width:100px'
	));
echo CHtml::link($img, $model->getUrl(), array('class' => 'pull-left'));
?>

	<div class="media-body">
       <h1 class="media-heading"><?php echo $model->getTitle(); ?></h1>
	<p><?php echo Yii::t('groupsModule.common',
				'Участники группы:') . ' ' . CHtml::link(Yii::t('groupsModule.common',
                    '{n} человек|{n} человека|{n} человек',
					$model->groupUsersCount),
				array(
				     '/groups/default/members',
				     'id' => $model->getId()
				)); ?></p>
       <p><?php echo $model->getDescription(); ?></p>
   </div>
	<div class="pull-right">
		<?php $this->widget('application.modules.groups.widgets.GroupOperations',
			array(
			     'group' => $model
			)); ?>
	</div>
</div>

<hr />