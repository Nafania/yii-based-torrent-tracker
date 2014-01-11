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

	<div class="media-body">
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
		<p>
			<?php
			echo Yii::t('groupsModule.common',
				'Мой статус:') . ' <span class="statusLabel">' .
				$data->groupUsers[0]->getStatusLabel() . '</span>';
			?>
			<?php
			if ( $data->groupUsers[0]->status == GroupUser::STATUS_INVITED ) {
				$this->widget('bootstrap.widgets.TbButton',
					array(
					     'buttonType'  => 'link',
					     'type'        => 'primary',
					     'size' => 'mini',
					     'label'       => Yii::t('groupsModule.common', 'Принять приглашение'),
					     'url'         => array(
						     '/groups/default/changeMemberStatus',
						     'gId'    => $data->getId(),
						     'uId'    => Yii::app()->getUser()->getId(),
						     'status' => GroupUser::STATUS_APPROVED
					     ),
					     'htmlOptions' => array(
						     'data-action'       => 'changeStatus',
						     'data-loading-text' => Yii::t('groupsModule.common', 'Идет отправка запроса'),
					     )
					));
				?>
				<?php
				$this->widget('bootstrap.widgets.TbButton',
					array(
					     'buttonType'  => 'link',
					     'label'       => Yii::t('groupsModule.common', 'Отклонить приглашение'),
					     'size' => 'mini',
					     'url'         => array(
						     '/groups/default/changeMemberStatus',
						     'gId'    => $data->getId(),
						     'uId'    => Yii::app()->getUser()->getId(),
						     'status' => GroupUser::STATUS_INVITE_DECLINED
					     ),
					     'htmlOptions' => array(
						     'data-action'       => 'changeStatus',
						     'data-loading-text' => Yii::t('groupsModule.common', 'Идет отправка запроса'),
					     )
					));
			}
			?>
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