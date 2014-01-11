<?php
/* @var $this DefaultController */
/* @var $data User */
/* @var $group Group */
/* @var $status integer */
?>
<div class="media commentContainer">

	<?php
	$img = $data->profile->getImageUrl(80, 80);
	$alt = $data->getName();
	$url = $data->getUrl();
	echo CHtml::link(CHtml::image($img,
			$alt,
			array(
			     'class'  => 'media-object',
			     'width'  => '80',
			     'height' => '80'
			)),
		$url,
		array('class' => 'pull-left'));
	?>

	<div class="media-body">
        <h3 class="media-heading">
	        <?php
	        echo CHtml::link($data->getName(), $data->getUrl());
	        ?>
	    </h3>
		<?php
		if ( Yii::app()->user->checkAccess('updateMembersStatusInOwnGroup',
				array('ownerId' => $group->ownerId)) || Yii::app()->user->checkAccess('updateGroup')
		) {
			?>

			<?php
			if ( $status != GroupUser::STATUS_APPROVED ) {
				$this->widget('bootstrap.widgets.TbButton',
					array(
					     'buttonType'  => 'link',
					     'type'        => 'primary',
					     'label'       => Yii::t('GroupsModule.common', 'Approve'),
					     'url'         => array(
						     '/groups/default/changeMemberStatus',
						     'gId'    => $group->getId(),
						     'uId'    => $data->getId(),
						     'status' => GroupUser::STATUS_APPROVED
					     ),
					     'htmlOptions' => array(
						     'data-action'       => 'changeStatus',
						     'data-loading-text' => Yii::t('groupsModule.common', 'Идет отправка запроса'),
					     )
					));
			}
			?>

			<?php
			if ( $status != GroupUser::STATUS_DECLINED && $data->getId() != $group->ownerId ) {
				$this->widget('bootstrap.widgets.TbButton',
					array(
					     'buttonType'  => 'link',
					     'type'        => 'danger',
					     'label'       => Yii::t('GroupsModule.common', 'Decline'),
					     'url'         => array(
						     '/groups/default/changeMemberStatus',
						     'gId'    => $group->getId(),
						     'uId'    => $data->getId(),
						     'status' => GroupUser::STATUS_DECLINED
					     ),
					     'htmlOptions' => array(
						     'data-action'       => 'changeStatus',
						     'data-loading-text' => Yii::t('groupsModule.common', 'Идет отправка запроса'),
					     )
					));
			}
			?>
		<?php } ?>
	</div>

</div>